<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Raffle;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-s-ticket';
    protected static ?string $navigationGroup = 'Rifas y Sorteos';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Boleta o Ticket';
    protected static ?string $pluralModelLabel = 'Boletas o Tickets';

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
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('package_id', null);
                                $set('numbers_count', 5);
                            })
                            ->required(),

                        Select::make('package_id')
                            ->label('Paquete de Números')
                            ->options(function () {
                                // 🟢 Obtenemos el array plano [5, 10, 20, 50]
                                $multiples = \App\Models\RaffleConfiguration::getVal('package_multiples', []);
                                
                                if (!is_array($multiples)) return [];

                                // 🟢 Estructuramos para Filament: ['5' => '5 Boletas', '10' => '10 Boletas', ...]
                                return collect($multiples)->mapWithKeys(function ($value) {
                                    $valStr = (string)$value;
                                    return [$valStr => "{$valStr} Boletas"];
                                })->toArray();
                            })
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                // 🟢 Al ser un array plano, el estado seleccionado ('5', '10', etc.) 
                                // es directamente la cantidad de números que se deben generar.
                                $set('numbers_count', $state ? (int)$state : 5);
                            })
                            ->required(),

                        TagsInput::make('ticket_numbers')
                            ->label('Números de la suerte')
                            ->required()
                            ->formatStateUsing(function ($state) {
                                if (is_string($state)) {
                                    $state = json_decode($state, true);
                                }
                                if (is_object($state)) {
                                    $state = json_decode(json_encode($state), true);
                                }
                                return $state['numbers'] ?? $state ?? [];
                            })
                            ->rules([
                                function ($get, $component) {
                                    return function (string $attribute, $value, $fail) use ($get, $component) {
                                        $raffleId = $get('raffle_id');
                                        if (empty($raffleId)) {
                                            $fail("Debes seleccionar una rifa primero.");
                                            return;
                                        }

                                        $raffle = Raffle::find($raffleId);
                                        $length = $raffle ? $raffle->digits_count : 3;

                                        $actualNumbers = is_array($value) && isset($value['numbers']) ? $value['numbers'] : (array)$value;

                                        $currentId = $component->getContainer()->getModel() instanceof Ticket 
                                            ? $component->getContainer()->getModel()->id 
                                            : 0;

                                        foreach ($actualNumbers as $number) {
                                            $number = (string)$number;

                                            if (strlen($number) !== $length) {
                                                $fail("El número {$number} debe tener exactamente {$length} cifras.");
                                                return;
                                            }

                                            $exists = Ticket::where('raffle_id', $raffleId)
                                                ->whereJsonContains('ticket_numbers->numbers', $number)
                                                ->where('id', '!=', $currentId)
                                                ->exists();

                                            if ($exists) {
                                                $fail("El número {$number} ya ha sido vendido o asignado en esta rifa.");
                                                return;
                                            }
                                        }
                                    };
                                },
                            ])
                            ->suffixAction(
                                Action::make('generarAleatorios')
                                    ->icon('heroicon-s-sparkles')
                                    ->label('Generar Aleatorios')
                                    ->action(function ($get, $set) {
                                        $raffleId = $get('raffle_id');

                                        if (empty($raffleId)) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Error')
                                                ->body('Debes seleccionar una rifa antes de generar números.')
                                                ->danger()
                                                ->send();
                                            return;
                                        }

                                        $raffle = Raffle::find($raffleId);
                                        if (!$raffle) return;

                                        $length = $raffle->digits_count;
                                        $count = $get('numbers_count') ?: 5;

                                        $randomNumbers = [];
                                        $attempts = 0;
                                        $maxAttempts = 300;

                                        while (count($randomNumbers) < $count && $attempts < $maxAttempts) {
                                            $attempts++;

                                            $min = pow(10, $length - 1);
                                            $max = pow(10, $length) - 1;
                                            $num = str_pad(rand($min, $max), $length, '0', STR_PAD_LEFT);

                                            if (in_array($num, $randomNumbers)) continue;

                                            $yaVendido = Ticket::where('raffle_id', $raffleId)
                                                ->whereJsonContains('ticket_numbers->numbers', $num)
                                                ->exists();

                                            if (!$yaVendido) {
                                                $randomNumbers[] = $num;
                                            }
                                        }

                                        if (count($randomNumbers) < $count) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Sin números suficientes')
                                                ->body('No se encontraron suficientes combinaciones libres.')
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

                        Hidden::make('numbers_count')->default(5),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('raffle.title')->label('Rifa')->searchable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('customer_phone')->label('Teléfono')->searchable(),
                Tables\Columns\TextColumn::make('ticket_numbers')
                    ->label('Números')
                    ->formatStateUsing(function ($state) {                        
                        $array = $state['numbers'] ?? $state ?? [];
                        return is_array($array) ? implode(', ', $array) : $state;
                    })
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Valor')
                    ->getStateUsing(function (Ticket $record) {
                        $raffle = $record->raffle;
                        $numbers = $record->ticket_numbers['numbers'] ?? $record->ticket_numbers ?? [];
                        $ticketCount = is_array($numbers) ? count($numbers) : 0;
                        return $raffle ? '$' . number_format($ticketCount * $raffle->ticket_price, 0, ',', '.') : '$0';
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Estado de Pago')
                    ->badge()
                    ->formatStateUsing(fn ($state) => __($state))
                    ->color(fn (string $state): string => match ($state) {
                        'PAID' => 'success',
                        'PENDING' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('raffle_id')->relationship('raffle', 'title')->label('Rifa'),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(['PAID' => 'Pagado', 'PENDING' => 'Pendiente'])
                    ->label('Estado de Pago'),
            ])
            ->actions([
                Tables\Actions\Action::make('descargarTicket')
                    ->label('Descargar Ticket')
                    ->tooltip('Descargar Ticket en PDF')
                    ->icon('heroicon-s-document-arrow-down')
                    ->color('danger')
                    ->iconButton()
                    ->action(function ($record) {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', [
                            'ticket' => $record,
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, "ticket-{$record->id}.pdf");
                    }),
                
                Tables\Actions\Action::make('enviarWhatsApp')
                    ->label('Enviar WhatsApp')
                    ->tooltip('Enviar detalles del ticket por WhatsApp')
                    ->icon('heroicon-s-chat-bubble-left-right')
                    ->color('success')
                    ->iconButton()
                    ->url(function (Ticket $record) {
                        $numero = preg_replace('/[^0-9]/', '', $record->customer_phone);
                        if (strlen($numero) === 10 && str_starts_with($numero, '3')) {
                            $numero = '57' . $numero;
                        }

                        $numbersArray = $record->ticket_numbers['numbers'] ?? $record->ticket_numbers ?? [];
                        $numerosList = is_array($numbersArray) ? implode(', ', $numbersArray) : $numbersArray;
                        $urlResultados = "https://tudominio.com/resultados"; 
                        $estadoPago = $record->payment_status === 'PAID' ? '🟢 PAGADO' : '🔴 PENDIENTE';

                        $mensaje = "🎉 *¡Felicidades {$record->customer_name}!* 🎉\n\n"
                                 . "🍀 ¡Bienvenido(a) a *El Palomo Negro*! 🍀\n\n"
                                 . "Estás participando en la rifa:\n"
                                 . "🔥 *{$record->raffle->title}* 🔥\n\n"
                                 . "🎟️ *Tus números de suerte:*\n"
                                 . "{$numerosList}\n\n"
                                 . "Estado del pago: *{$estadoPago}*\n\n"
                                 . "📲 *Revisa los resultados aquí:*\n"
                                 . "👉 {$urlResultados}\n\n"
                                 . "✨ ¡Que la suerte te acompañe! ✨";

                        return "https://api.whatsapp.com/send?phone={$numero}&text=" . rawurlencode($mensaje);
                    })
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make()
                    ->tooltip('Ver Detalles del Ticket')
                    ->iconButton()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->tooltip('Editar Ticket')
                    ->iconButton()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Eliminar Ticket')
                    ->iconButton()
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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