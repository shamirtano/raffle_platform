<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketOrderResource\Pages;
use App\Models\Ticket;
use App\Models\TicketOrder;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class TicketOrderResource extends Resource
{
    protected static ?string $model = TicketOrder::class;

    protected static ?string $navigationIcon = 'heroicon-m-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Pedidos Web';
    
    protected static ?string $modelLabel = 'Pedido';
    
    protected static ?string $pluralModelLabel = 'Pedidos';

    // BADGE DINÁMICO EN EL MENÚ DE NAVEGACIÓN: Muestra el conteo de pedidos pendientes
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    // COLOR DEL BADGE: Lo pone en un tono llamativo (ej: ámbar/amarillo)
    public static function getNavigationBadgeColor(): ?string
    {        
        return 'primary';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([                
                Forms\Components\Card::make()
                ->schema([
                    Placeholder::make('expiration_timer')
                        ->label('')
                        ->visible(fn ($record) => $record && $record->status === 'pending')
                        ->content(function ($record) {                            
                            $createdAt = $record->created_at->timestamp * 1000;
                            $durationMinutes = 10; 
                            $expiresAt = $createdAt + ($durationMinutes * 60 * 1000);

                            return new HtmlString("
                                <div class='flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800'
                                    x-data=\"{
                                        timeLeft: '',
                                        expiresAt: {$expiresAt},
                                        isExpired: false,
                                        init() {
                                            let updateTimer = () => {
                                                let now = new Date().getTime();
                                                let diff = this.expiresAt - now;
                                                
                                                if (diff <= 0) {
                                                    this.timeLeft = '00:00';
                                                    this.isExpired = true;
                                                    clearInterval(timerInterval);
                                                    return;
                                                }
                                                
                                                let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                let seconds = Math.floor((diff % (1000 * 60)) / 1000);
                                                
                                                minutes = minutes < 10 ? '0' + minutes : minutes;
                                                seconds = seconds < 10 ? '0' + seconds : seconds;
                                                
                                                this.timeLeft = minutes + ':' + seconds;
                                            };
                                            updateTimer();
                                            let timerInterval = setInterval(updateTimer, 1000);
                                        }
                                    }\">
                                    
                                    <span class='text-xs font-bold uppercase tracking-wider text-gray-900 mb-2'>
                                        Tiempo Restante de Reserva
                                    </span>

                                    <div :class=\"isExpired ? 'bg-danger-600' : 'bg-primary-600 animate-pulse'\" 
                                        class='w-24 h-24 rounded-full flex items-center justify-center shadow-lg transition-colors duration-500'>
                                        <span x-text='timeLeft' class='text-dark font-mono text-2xl p-4 font-black tracking-tight'>
                                            --:--
                                        </span>
                                    </div>

                                    <template x-if='isExpired'>
                                        <span class='text-danger-600 text-xs font-bold mt-2 animate-bounce'>
                                            Tiempo límite superado. ¡Libera los números!
                                        </span>
                                    </template>
                                </div>
                            ");
                        })
                        ->columnSpanFull(),
                    Forms\Components\Select::make('raffle_id')
                        ->relationship('raffle', 'title')
                        ->label('Rifa')
                        ->required()
                        ->disabledOn('edit')
                        ->live(), // Hace que el formulario reaccione al cambiar de rifa

                    Forms\Components\TextInput::make('customer_name')
                        ->label('Nombre del Cliente')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('customer_phone')
                        ->label('WhatsApp')
                        ->tel()
                        ->required()
                        ->maxLength(20),

                    Forms\Components\TagsInput::make('ticket_numbers')
                        ->label('Números Reservados')
                        ->placeholder('Ej: 02, 45 y presiona Enter')
                        ->required()
                        
                        // BOTÓN DE VALIDACIÓN INTERNA
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('validateNumbers')
                                ->icon('heroicon-m-magnifying-glass')
                                ->color('info')
                                ->tooltip('Verificar disponibilidad de estos números')
                                ->action(function (Forms\Get $get, $state) {
                                    $raffleId = $get('raffle_id');

                                    if (!$raffleId) {
                                        Notification::make()
                                            ->title('Falta seleccionar la rifa')
                                            ->body('Por favor selecciona una rifa primero para poder validar los números.')
                                            ->danger()
                                            ->send();
                                        return;
                                    }

                                    if (empty($state) || !is_array($state)) {
                                        Notification::make()
                                            ->title('No hay números')
                                            ->body('Ingresa al menos un número para realizar la verificación.')
                                            ->warning()
                                            ->send();
                                        return;
                                    }

                                    $takenTickets = DB::table('tickets')
                                        ->where('raffle_id', $raffleId)
                                        ->get()
                                        ->flatMap(function ($t) {
                                            $decoded = json_decode($t->ticket_numbers, true);
                                            return $decoded['numbers'] ?? $decoded ?? [];
                                        })->toArray();

                                    // 4. Consultar números ocupados en otras órdenes pendientes
                                    $takenOrders = DB::table('ticket_orders')
                                        ->where('raffle_id', $raffleId)
                                        ->where('status', 'pending')
                                        // Opcional: Si estamos editando, ignorar los números de este mismo registro
                                        ->when($get('id'), fn($q) => $q->where('id', '!=', $get('id')))
                                        ->get()
                                        ->flatMap(fn($o) => json_decode($o->ticket_numbers, true) ?? [])
                                        ->toArray();

                                    $allTakenNumbers = array_merge($takenTickets, $takenOrders);

                                    $occupied = array_intersect($state, $allTakenNumbers);

                                    if (empty($occupied)) {
                                        Notification::make()
                                            ->title('¡Números Disponibles!')
                                            ->body('Todos los números ingresados están libres para ser apartados.')
                                            ->success()
                                            ->send();
                                    } else {
                                        $listaOcupados = implode(', ', $occupied);
                                        Notification::make()
                                            ->title('¡Atención: Números Ocupados!')
                                            ->body("Los siguientes números ya no están disponibles: **{$listaOcupados}**. Por favor remuévelos.")
                                            ->danger()
                                            ->persistent()
                                            ->send();
                                    }
                                })
                        ),

                    Forms\Components\Select::make('status')
                        ->label('Estado del Pedido')
                        ->options([
                            'pending' => 'Pendiente',
                            'processed' => 'Procesado',
                        ])
                        ->required()
                        ->default('pending'),
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('raffle.title')
                    ->label('Rifa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ticket_numbers')
                    ->label('Números')
                    ->searchable()                    
                    ->wrap()
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        $decoded = json_decode($state, true);
                        if (is_array($decoded)) {
                            return implode(', ', $decoded);
                        }
                        return $state ?? '';
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()                    
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processed' => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'processed' => 'Procesado',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar Estado')
                    ->options([
                        'pending' => 'Pendientes',
                        'processed' => 'Procesados',
                    ]),
            ])
            ->actions([
                // Si el pedido ya aparece procesado, mostrar el botón para enviar por whatsapp
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('Notificar por WhatsApp')
                    ->tooltip('Enviar mensaje de confirmación al cliente por WhatsApp')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->color('success')                    
                    ->url(function (TicketOrder $record) {
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
                    ->openUrlInNewTab()
                    ->visible(fn (TicketOrder $record) => $record->status === 'processed')
                    ->iconButton(),

                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalles')
                    ->tooltip('Ver toda la información del pedido')
                    ->icon('heroicon-m-eye')
                    ->color('primary')
                    ->iconButton(),

                Tables\Actions\EditAction::make()
                    ->label('Editar Pedido')
                    ->tooltip('Modificar información del pedido')
                    ->icon('heroicon-m-pencil')
                    ->color('secondary')
                    ->iconButton(),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('admin')) // Restricción opcional para administradores
                    ->label('Eliminar Pedido')
                    ->tooltip('Eliminar este pedido de forma permanente')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('admin')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketOrders::route('/'),
            'create' => Pages\CreateTicketOrder::route('/create'),
            'edit' => Pages\EditTicketOrder::route('/{record}/edit'),
        ];
    }
}