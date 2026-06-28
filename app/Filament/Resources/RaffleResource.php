<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RaffleResource\Pages;
use App\Filament\Resources\RaffleResource\RelationManagers;
use App\Models\Raffle;
use App\Models\RaffleConfiguration;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RaffleResource extends Resource
{
    protected static ?string $model = Raffle::class;
    protected static ?string $navigationIcon = 'heroicon-s-ticket';
    protected static ?string $navigationGroup = 'Rifas y Sorteos';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Rifa';
    protected static ?string $pluralModelLabel = 'Rifas';

    /**
     * Manejar los permisos de usuario para acceder a este recurso
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->can('view_raffles') || $user->can('manage_raffles'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECCIÓN: Información General
                Forms\Components\Section::make('📝 Información General del Sorteo')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Título')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    if (empty($get('slug'))) {
                                        $set('slug', \Str::slug($state));
                                    }
                                }),

                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->unique(ignoreRecord: true)
                                ->disabledOn('edit')
                                ->dehydrated(),
                        ]),

                        Forms\Components\Textarea::make('description')->label('Descripción')->rows(3)->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('image_path')
                            ->label('Imagen Publicitaria')
                            ->directory('raffle_images')
                            ->disk('public')
                            ->image()
                            ->columnSpanFull(),
                    ]),

                // NUEVA SECCIÓN: Datos de Organización y Contacto
                Forms\Components\Section::make('🏢 Datos del Organizador')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('organizer')
                                ->label('Organizado por')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('contact_info')
                                ->label('Teléfono / WhatsApp de Contacto')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('social_media_url')
                                ->label('Enlace de Redes Sociales')
                                ->url()
                                ->maxLength(255),
                        ]),
                    ]),

                // SECCIÓN: Reglas de Juego y Parámetros Globales
                Forms\Components\Section::make('🎰 Reglas de Juego')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\Select::make('game_type')
                                ->label('Tipo de Juego')
                                ->options(fn () => RaffleConfiguration::getVal('raffle_types', ['traditional' => 'Tradicional']))
                                ->required(),

                            // 🌟 Campo de Cifras Dinámico conectado a la Configuración Global
                            Forms\Components\Select::make('digits_count')
                                ->label('Cantidad de Cifras')
                                ->options(function () {
                                    $digits = RaffleConfiguration::getVal('digits_options', [3, 4]);
                                    return array_combine($digits, array_map(fn($d) => "{$d} Cifras", $digits));
                                })
                                ->default(3)
                                ->required(),

                            Forms\Components\Select::make('reference_lottery')
                                ->label('Lotería del Sorteo')
                                ->options(fn () => RaffleConfiguration::getVal('allowed_lotteries', []))
                                ->searchable()
                                ->required(),
                        ]),
                    ]),

                // SECCIÓN: Costos, Premios y Logística
                Forms\Components\Section::make('💰 Costos y Premios')
                    ->schema([
                        Grid::make(3)->schema([
                            Forms\Components\Select::make('prize_type')
                                ->label('Tipo de Premio')
                                ->options([1 => '💵 Dinero', 2 => '📦 Artículo'])
                                ->required(),

                            Forms\Components\TextInput::make('jackpot_prize')
                                ->label('Premio Mayor')
                                ->prefix('$')
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('ticket_price')
                                ->label('Precio del Ticket')
                                ->prefix('$')
                                ->numeric()
                                ->required(),
                        ]),

                        Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('draw_date')->label('Fecha del Sorteo')->required(),
                            Forms\Components\Select::make('status')
                                ->label('Estado')
                                ->options(['OPEN' => '🟢 Abierta', 'CLOSED' => '🔴 Cerrada'])
                                ->default('OPEN')
                                ->required(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->label('Título'),                
                Tables\Columns\TextColumn::make('prize_type')->label('Tipo de Premio')                    
                    ->formatStateUsing(fn ($state) => $state == 1 ? '💵 Dinero' : '📦 Artículo'),
                Tables\Columns\TextColumn::make('jackpot_prize')
                    ->prefix('$')
                    ->label('Premio en pesos')
                    ->formatStateUsing(function ($state) {
                        if ($state === null || $state === '') return '';
                        return number_format((int)$state, 0, ',', '.');
                    }),
                Tables\Columns\TextColumn::make('ticket_price')
                    ->prefix('$')
                    ->label('Precio')
                    ->formatStateUsing(function ($state) {
                        if ($state === null || $state === '') return '';
                        return number_format((int)$state, 0, ',', '.');
                    }),
                Tables\Columns\TextColumn::make('reference_lottery')
                    ->label('Lotería')
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                Tables\Columns\TextColumn::make('draw_date')->date()->label('Fecha de Sorteo'),
                Tables\Columns\BadgeColumn::make('status')->label('Estado')                    
                    ->colors([
                        'success' => 'OPEN',
                        'danger' => 'CLOSED',
                    ])
                    ->formatStateUsing(fn ($state) => __($state)),
                // Contar tickets vendidos por rifa usando el método estático del modelo Ticket countTicketsByRaffle()
                Tables\Columns\TextColumn::make('tickets_sold')->label('Vendidos')
                    // Raffle::soldTickets() devuelve la cantidad de tickets vendidos
                    ->getStateUsing(function (Raffle $record) {                        
                        $countData = \App\Models\Ticket::countTicketsByRaffle();
                        $raffleData = $countData->firstWhere('raffle_id', $record->id);
                        return $raffleData ? $raffleData->total_tickets : 0;
                    }),
                
            ])
            ->filters([                
                // tipo de premio
                Tables\Filters\SelectFilter::make('prize_type')
                    ->options([1 => '💵 Dinero', 2 => '📦 Artículo'])
                    ->label('Tipo de Premio'),
                // estado
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'OPEN' => '🟢 Abierta',
                        'CLOSED' => '🔴 Cerrada',
                    ])
                    ->label('Estado'),
                // Lotería de referencia
                Tables\Filters\SelectFilter::make('reference_lottery')
                    ->options(function () {
                        return \App\Models\RaffleConfiguration::getVal('allowed_lotteries', []);
                    })
                    ->label('Lotería de Referencia'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver detalles')
                    ->tooltip('Ver detalles de la rifa, incluyendo números vendidos y pendientes')
                    ->icon('heroicon-s-eye')
                    ->color('info')
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->tooltip('Editar los detalles de la rifa')
                    ->icon('heroicon-s-pencil')
                    ->color('warning')
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->tooltip('Eliminar esta rifa de forma permanente')
                    ->icon('heroicon-s-trash')
                    ->color('danger')
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRaffles::route('/'),
            'create' => Pages\CreateRaffle::route('/create'),
            'edit' => Pages\EditRaffle::route('/{record}/edit'),
        ];
    }

    /**
     * En el EditRaffle.php dentro de protected function handleRecordUpdate($record, array $data)
     * sobrescribimos este método para eliminar la imagen anterior si se sube una nueva, evitando acumular archivos no utilizados en el almacenamiento.
     * @param Model $record
     * @param array $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Eliminar imagen anterior si se subió una nueva
        if (isset($data['image_path']) && $record->image_path) {
            $oldPath = 'raffle_images/' . $record->image_path;
            Storage::disk('public')->delete($oldPath);
        }

        $record = parent::handleRecordUpdate($record, $data);

        return $record;
    }

    /**
     * Modifica los datos del formulario antes de crear el registro en la base de datos.
     * @param array $data
     * @return array
     */
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        // Si el slug llegó vacío o nulo, lo forzamos a partir del título
        if (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = \Str::slug($data['title']);
        }

        return $data;
    }

    // Este método se puede usar para mutar los datos del formulario antes de que se llenen en el formulario, pero en este caso no necesitamos hacer ninguna mutación específica, así que simplemente devolvemos los datos sin cambios.
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }
}
