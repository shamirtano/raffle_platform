<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Raffle;
use App\Models\Ticket;
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
use App\Models\Package;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $modelLabel = 'Boleta o Ticket';
    protected static ?string $pluralModelLabel = 'Boletas o Tickets';

    /**
     * Manejar los permisos de usuario para acceder a este recurso
     */
    public static function canViewAny(): bool
    {
        // Permitir a admins y vendedores ver la lista
        return auth()->user()->hasAnyRole(['admin', 'seller', 'partner']);
    }

    public static function canCreate(): bool
    {
        // Solo permitir crear si eres admin o vendedor
        return auth()->user()->hasAnyRole(['admin', 'seller', 'partner']);
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
                                    // Buscamos la rifa para obtener su configuración
                                    $raffle = \App\Models\Raffle::find($raffleId);
                                    $length = $raffle ? $raffle->digits_count : 3; // Por defecto 3 cifras

                                    foreach ($value as $number) {
                                        // 1. Validar longitud de cifras
                                        if (strlen((string)$number) !== $length) {
                                            $fail("El número {$number} debe tener exactamente {$length} cifras.");
                                        }

                                        // 2. Validar que no esté vendido
                                        $exists = \App\Models\Ticket::where('raffle_id', $raffleId)
                                            ->whereJsonContains('ticket_numbers', $number)
                                            ->where('id', '!=', $get('id'))
                                            ->exists();

                                        if ($exists) {
                                            $fail("El número {$number} ya está vendido para esta rifa.");
                                        }
                                    }
                                };
                            },
                        ])
                        ->suffixAction(
                            Action::make('generarAleatorios')
                                ->icon('heroicon-o-sparkles')
                                ->label('Generar')
                                ->action(function ($state, $get, $set) {
                                    $count = $get('numbers_count') ?: 5;
                                    $raffleId = $get('raffle_id');
                                    $randomNumbers = [];
                                    while (count($randomNumbers) < $count) {
                                        $num = (string)rand(100, 999);
                                        if (!in_array($num, $randomNumbers) && 
                                            !\App\Models\Ticket::where('raffle_id', $raffleId)->whereJsonContains('ticket_numbers', $num)->exists()) {
                                            $randomNumbers[] = $num;
                                        }
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
                Tables\Columns\TextColumn::make('customer_name')->label('Cliente'),
                Tables\Columns\TextColumn::make('ticket_numbers')->label('Números'),
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
                    ->label('Ticket PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Ticket $record) {
                        $pdf = Pdf::loadView('pdf.ticket', ['ticket' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "ticket-{$record->id}.pdf"
                        );
                    }),
                // Enviar por Whatsapp, agregar ícono y acción para enviar el ticket por WhatsApp --- IGNORE ---
                Tables\Actions\Action::make('enviarWhatsApp')
                    ->label('Enviar por WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->url(function (Ticket $record) {
                        $numero = preg_replace('/[^0-9]/', '', $record->customer_phone);
                        $numerosList = implode(', ', $record->ticket_numbers);
                        $urlResultados = "https://tudominio.com/resultados";
                        $estadoPago = __($record->payment_status);

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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
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
