<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\Package;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-m-ticket';
    protected static ?string $modelLabel = 'Boleta o Ticket';
    protected static ?string $pluralModelLabel = 'Boletas o Tickets';

    /**
     * Manejar los permisos de usuario para acceder a este recurso
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->can('view_tickets') || $user->can('sell_tickets') || $user->can('manage_tickets'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Cliente')
                    ->schema([
                        TextInput::make('customer_name')->label('Nombre del Cliente')->required(),
                        TextInput::make('customer_phone')->label('Teléfono')->tel()->required(),
                ])->columns(2),

            Section::make('Detalles de la Venta')
                ->schema([
                    Select::make('raffle_id')
                        ->label('Rifa')
                        ->relationship('raffle', 'title')
                        ->required(),

                    Select::make('package_id')
                        ->label('Paquete de Números')
                        ->relationship('package', 'name')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $package = \App\Models\Package::find($state);
                            $set('numbers_count', $package ? $package->quantity : 0);
                        }),

                    TagsInput::make('ticket_numbers')
                        ->label('Números de la suerte')
                        ->required()
                        ->rules([
                            function ($get) {
                                return function (string $attribute, $value, $fail) use ($get) {
                                    $raffleId = $get('raffle_id');

                                    if (empty($raffleId)) {
                                        $fail("Debes seleccionar una rifa primero.");
                                        return;
                                    }

                                    $raffle = \App\Models\Raffle::find($raffleId);
                                    $length = $raffle ? $raffle->digits_count : 3;

                                    foreach ($value as $number) {
                                        $number = (string)$number;

                                        // Validar longitud
                                        if (strlen($number) !== $length) {
                                            $fail("El número {$number} debe tener exactamente {$length} cifras.");
                                        }

                                        // Validar que no esté vendido por otro vendedor o comprado
                                        $exists = \App\Models\Ticket::where('raffle_id', $raffleId)
                                            ->whereJsonContains('ticket_numbers', $number)
                                            ->where('id', '!=', $get('id') ?? 0)
                                            ->exists();

                                        if ($exists) {
                                            $fail("El número {$number} ya ha sido vendido o asignado en esta rifa.");
                                        }
                                    }
                                };
                            },
                        ])
                        ->suffixAction(
                            Action::make('generarAleatorios')
                                ->icon('heroicon-m-sparkles')
                                ->label('Generar Aleatorios')
                                ->action(function ($get, $set) {
                                    $raffleId = $get('raffle_id');

                                    // Validación: Debe haber seleccionado una rifa
                                    if (empty($raffleId)) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Error')
                                            ->body('Debes seleccionar una rifa antes de generar números.')
                                            ->danger()
                                            ->send();
                                        return;
                                    }

                                    $raffle = \App\Models\Raffle::find($raffleId);
                                    if (!$raffle) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Error')
                                            ->body('Rifa no encontrada.')
                                            ->danger()
                                            ->send();
                                        return;
                                    }

                                    $length = $raffle->digits_count;
                                    $count = $get('numbers_count') ?: 5;

                                    $randomNumbers = [];
                                    $attempts = 0;
                                    $maxAttempts = 200; // Mayor seguridad

                                    while (count($randomNumbers) < $count && $attempts < $maxAttempts) {
                                        $attempts++;

                                        $min = pow(10, $length - 1);
                                        $max = pow(10, $length) - 1;
                                        $num = (string) rand($min, $max);

                                        // Verificar que no esté duplicado en esta generación
                                        if (in_array($num, $randomNumbers)) {
                                            continue;
                                        }

                                        // Verificar que NO esté vendido por otro vendedor
                                        $yaVendido = \App\Models\Ticket::where('raffle_id', $raffleId)
                                            ->whereJsonContains('ticket_numbers', $num)
                                            ->exists();

                                        if (!$yaVendido) {
                                            $randomNumbers[] = $num;
                                        }
                                    }

                                    if (empty($randomNumbers)) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Sin números disponibles')
                                            ->body('No se encontraron números disponibles. La rifa puede estar casi llena.')
                                            ->warning()
                                            ->send();
                                        return;
                                    }

                                    $set('ticket_numbers', $randomNumbers);
                                })
                        ),

                    Select::make('payment_status')
                        ->label('Estado de Pago')
                        ->options(['PAID' => 'Pagado', 'PENDING' => 'Pendiente'])
                        ->default('PENDING')
                        ->required(),

                    Hidden::make('numbers_count'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // Si el usuario NO es admin, solo puede ver sus propios tickets
                if (!auth()->user()->hasRole('admin')) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('raffle.title')->label('Rifa')->searchable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('ticket_numbers')->label('Números')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        return $state;
                    })
                    ->wrap()
                    ->searchable(),                
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Valor')
                    ->getStateUsing(function (Ticket $record) {
                        $raffle = $record->raffle;
                        $ticketCount = is_array($record->ticket_numbers) ? count($record->ticket_numbers) : 0;
                        return $raffle ? '$' . number_format($ticketCount * $raffle->ticket_price, 0, ',', '.') : '$0';
                    }),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Estado de Pago')
                    ->formatStateUsing(fn ($state) => __($state))
                    ->colors(['success' => 'PAID', 'warning' => 'PENDING']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('raffle_id')
                    ->relationship('raffle', 'title')
                    ->label('Rifa'),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(['PAID' => 'Pagado', 'PENDING' => 'Pendiente'])
                    ->label('Estado de Pago'),
            ])
            ->actions([
                Tables\Actions\Action::make('descargarTicket')
                    ->label('Descargar Ticket')
                    ->tooltip('Descargar Ticket en PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('danger')
                    ->iconButton(),
                // Enviar por Whatsapp, agregar ícono y acción para enviar el ticket por WhatsApp
                Tables\Actions\Action::make('enviarWhatsApp')
                    ->label('Enviar WhatsApp')
                    ->tooltip('Enviar detalles del ticket por WhatsApp')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')
                    ->iconButton()
                    ->url(function (Ticket $record) {
                        $numero = preg_replace('/[^0-9]/', '', $record->customer_phone);
                        if (strlen($numero) === 10 && str_starts_with($numero, '3')) {
                            $numero = '57' . $numero;
                        }

                        $numerosList = implode(', ', $record->ticket_numbers);
                        $urlResultados = "https://tudominio.com/resultados"; 
                        
                        // 🌟 CORREGIDO: Definición de la variable faltante
                        $estadoPago = 'PENDIENTE';

                        // 🌟 CORREGIDO: Texto alineado a la izquierda para evitar espacios raros en WhatsApp
                        $mensaje = <<<WHATSAPP
                            🎉 *¡Felicidades {$record->customer_name}!* 🎉

                            🍀 ¡Bienvenido/a a *El Palomo Negro*! 🍀         
                                    
                            Estás participando en la rifa:

                            🔥 *{$record->raffle->title}* 🔥

                            🎟️ *Tus números de suerte:*
                            {$numerosList}

                            Estado del pago: *{$estadoPago}*

                            Puedes realizar tu pagos mediante Nequi, Bancolombia o en Efectivo en nuestras oficinas.

                            📲 *Revisa los resultados aquí:*
                            👉 {$urlResultados}

                            ✨ ¡Que la suerte te acompañe y cruces los dedos! ✨

                            ¿Tienes alguna duda? Escríbenos 😊
                            WHATSAPP;

                            $textEncoded = rawurlencode($mensaje);

                            return "https://api.whatsapp.com/send?phone={$numero}&text={$textEncoded}";
                        })
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalles')
                    ->tooltip('Ver Detalles del Ticket')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->tooltip('Editar Ticket')
                    ->icon('heroicon-m-pencil')
                    ->color('warning')
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->tooltip('Eliminar Ticket')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->iconButton(),
            ])->actionsColumnLabel('Acciones')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->hasRole('admin')) {
            return $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
