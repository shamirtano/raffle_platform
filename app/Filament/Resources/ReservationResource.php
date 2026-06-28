<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use App\Models\RaffleConfiguration;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;
    protected static ?string $navigationIcon = 'heroicon-s-calendar-days';
    protected static ?string $navigationGroup = 'Reservas y Pedidos';
    protected static ?string $modelLabel = 'Reserva';
    protected static ?string $pluralModelLabel = 'Reservas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información del Cliente')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Nombre del Cliente')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('customer_phone')
                            ->label('Teléfono del Cliente')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                    ])->columns(2),

                Section::make('Detalles de la Reserva')
                    ->schema([
                        Select::make('area')
                            ->label('Área de la Reserva')
                            ->options(function () {                                
                                return \App\Models\LandingSetting::getVal('landing_reservation_areas', [
                                    'Interior' => 'Interior',
                                    'Exterior' => 'Exterior',
                                ]);
                            })
                            ->required(),

                        DateTimePicker::make('reservation_time')
                            ->label('Fecha y Hora de la Reserva')
                            ->native(false) // Despliega el calendario estilizado de Filament
                            ->displayFormat('d/m/Y H:i')
                            ->minutesStep(15) // Incrementos de 15 minutos en el reloj
                            ->required(),

                        TextInput::make('guests_count')
                            ->label('Cantidad de Invitados')
                            ->numeric()
                            ->minValue(1)
                            ->required(),

                        Select::make('status')
                            ->label('Estado de la Reserva')
                            ->options([
                                'pending' => 'Pendiente',
                                'confirmed' => 'Confirmada',
                                'cancelled' => 'Cancelada',
                            ])
                            ->default('pending')
                            ->required(),

                        Textarea::make('additional_notes')
                            ->label('Notas Adicionales')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Teléfono')
                    ->searchable(),

                Tables\Columns\TextColumn::make('area')
                    ->label('Área')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reservation_time')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('guests_count')
                    ->label('Invitados')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'cancelled' => 'Cancelada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmada',
                        'cancelled' => 'Cancelada',
                    ]),
                Tables\Filters\Filter::make('reservation_time')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('reservation_time', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('reservation_time', '<=', $date));
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar Reserva')
                    ->tooltip('Modificar información de la reserva')
                    ->icon('heroicon-s-pencil')
                    ->color('warning')
                    ->iconButton(),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}