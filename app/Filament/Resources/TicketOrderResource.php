<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketOrderResource\Pages;
use App\Models\Ticket;
use App\Models\TicketOrder;
use App\Models\Raffle;
use App\Models\RaffleConfiguration;
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

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';

    protected static ?string $navigationGroup = 'Rifas y Sorteos';
    
    protected static ?string $navigationLabel = 'Pedidos Web';

    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Pedido';
    
    protected static ?string $pluralModelLabel = 'Pedidos';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

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
                                                    this.timeLeft = '00:00';
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
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('ticket_numbers', []))
                        ->columnSpanFull(), 

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
                        ->helperText('Ingresa los números separados por comas o presiona Enter después de cada número. Ejemplo: 02, 45, 78. Luego presiona el botón buscar para validar que los números estén disponibles.')
                        ->placeholder('Ej: 02, 45 y presiona Enter')
                        ->required()
                        
                        // VALIDACIÓN EN TIEMPO DE GUARDADO (Formulario Filament)
                        ->rules([
                            function (Forms\Get $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $raffleId = $get('raffle_id');
                                    if (!$raffleId) return;

                                    $raffle = Raffle::find($raffleId);
                                    if (!$raffle) return;

                                    // 1. Validar Combos / Múltiplos Obligatorios
                                    $numbersCount = is_array($value) ? count($value) : 0;
                                    if (!$raffle->validateOrderPackages($numbersCount)) {
                                        $combosPermitidos = implode(', ', RaffleConfiguration::getVal('package_multiples', []));
                                        $fail("Cantidad no permitida. En El Palomo Negro se vende exclusivamente por paquetes de [{$combosPermitidos}] números. Ingresaste: {$numbersCount}.");
                                        return;
                                    }

                                    // 2. Validar Longitud de Cifras (Inyectando ceros o bloqueando)
                                    // Se asume el valor por defecto en caso de no encontrarse en la parametrización
                                    $requiredDigits = 3; 
                                    if ($raffle->game_type === 'traditional') {
                                        // Tradicional usa la lógica del seeder o valor manual según tu modelo
                                        $requiredDigits = $raffle->digits_count ?? 3;
                                    }

                                    foreach ($value as $number) {
                                        $cleanNumber = preg_replace('/[^0-9]/', '', $number);
                                        if (strlen($cleanNumber) !== $requiredDigits) {
                                            $fail("El número '{$number}' no es válido. La modalidad de esta rifa exige que todos los boletos tengan exactamente {$requiredDigits} cifras.");
                                            return;
                                        }
                                    }
                                };
                            }
                        ])
                        
                        ->suffixAction(
                            Forms\Components\Actions\Action::make('validateNumbers')
                                ->icon('heroicon-s-magnifying-glass')
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

                                    $takenOrders = DB::table('ticket_orders')
                                        ->where('raffle_id', $raffleId)
                                        ->where('status', 'pending')
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
                                ->disabled(fn (Forms\Get $get) => $get('status') === 'processed')
                        ),

                    Forms\Components\Select::make('status')
                        ->label('Estado del Pedido')
                        ->options([
                            'pending' => 'Pendiente',
                            'processed' => 'Procesado',
                        ])
                        ->helperText('Cambia el estado del pedido a "Procesado" una vez que se haya confirmado el pago y se haya generado el ticket.')
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
                        return is_array($decoded) ? implode(', ', $decoded) : ($state ?? '');
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
                Tables\Actions\Action::make('send_whatsapp')
                    ->label('Notificar por WhatsApp')
                    ->tooltip('Enviar confirmación al WhatsApp del cliente')
                    ->icon('heroicon-s-chat-bubble-left-right')
                    ->color('success')                    
                    ->url(function (TicketOrder $record) {
                        $numero = preg_replace('/[^0-9]/', '', $record->customer_phone);
                        if (strlen($numero) === 10 && str_starts_with($numero, '3')) {
                            $numero = '57' . $numero;
                        }

                        $numerosList = is_array($record->ticket_numbers) 
                            ? implode(', ', $record->ticket_numbers) 
                            : implode(', ', json_decode($record->ticket_numbers, true) ?? []);
                            
                        $urlResultados = "https://tudominio.com/resultados"; 
                        $estadoPago = 'PENDIENTE';

                        // Bloque para WhatsApp Web limpio
$mensaje = <<<WHATSAPP
🎉 *¡Felicidades {$record->customer_name}!* 🎉

🍀 ¡Bienvenido(a) a *El Palomo Negro*! 🍀         
                                    
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
                    ->tooltip('Ver detalles del pedido')
                    ->iconButton()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->label('Editar Pedido')
                    ->tooltip('Modificar información del pedido')
                    ->icon('heroicon-s-pencil')
                    ->color('warning')
                    ->iconButton()                    
                    ->disabled(fn (TicketOrder $record) => $record->status === 'processed')
                    ->visible(fn (TicketOrder $record) => $record->status !== 'processed'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user() && auth()->user()->id === 1) // Restricción básica de seguridad
                    ->label('Eliminar Pedido')
                    ->iconButton()
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user() && auth()->user()->id === 1),
                ]),
            ])
            ->actionsColumnLabel('Acciones');
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