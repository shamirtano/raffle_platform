<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RaffleResource\Pages;
use App\Filament\Resources\RaffleResource\RelationManagers;
use App\Models\Raffle;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Support\RawJs;
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
    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';
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
                Forms\Components\Section::make('Detalles de la Rifa')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->live()
                        ->afterStateUpdated(function (callable $set, $state, $get) {
                            if (empty($get('slug'))) {
                                $set('slug', \Str::slug($state));
                            }
                        }),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Se genera automáticamente desde el título. Puedes editarlo manualmente si lo deseas.')
                        ->disabledOn('edit')
                        ->dehydrated(),

                    Forms\Components\Textarea::make('description')
                        ->label('Descripción')
                        ->rows(3)
                        ->columnSpan(2),

                    Forms\Components\FileUpload::make('image_path')
                        ->label('Imagen de la Rifa')
                        ->directory('raffle-images')
                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file) {
                            $extension = $file->getClientOriginalExtension();
                            // Nombre único basado en timestamp + número aleatorio
                            return time() . rand(1000, 9999) . '.' . strtolower($extension);
                        })
                        ->image()
                        ->imageEditor()
                        ->helperText('Sube una imagen representativa. La imagen anterior se eliminará automáticamente al actualizar.')
                        ->maxSize(1024)
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('organizer')
                        ->label('Organizador')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('contact_info')
                        ->label('Información de Contacto')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('social_media_url')
                        ->label('URL de Redes Sociales')
                        ->maxLength(255),

                    Forms\Components\Select::make('prize_type')
                        ->label('Tipo de Premio')
                        ->options([
                            1 => '💵 Dinero',
                            2 => '📦 Artículo',
                        ])
                        ->required()
                        ->live(),

                    // Premio Jackpot
                    Forms\Components\TextInput::make('jackpot_prize')
                        ->label('Premio en pesos')
                        ->prefix('$')
                        ->numeric()                       
                        
                        ->formatStateUsing(function ($state) {
                            if ($state === null || $state === '') return '';                            
                            return (int) $state; 
                        })

                        ->mask(RawJs::make(<<<'JS'
                            function (value) {
                                if (!value) return '';
                                return value.toString().replace(/[^0-9]/g, '')
                                        .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            }
                        JS))
                        ->stripCharacters(['.'])
                        ->minValue(0)
                        ->maxValue(9999999999)
                        ->helperText('Ejemplo: 5.000.000')
                        ->required(),

                    // Precio del ticket
                    Forms\Components\TextInput::make('ticket_price')
                        ->label('Precio del Ticket')
                        ->prefix('$')
                        ->numeric()                       
                        
                        ->formatStateUsing(function ($state) {
                            if ($state === null || $state === '') return '';                            
                            return (int) $state; 
                        })

                        ->mask(RawJs::make(<<<'JS'
                            function (value) {
                                if (!value) return '';
                                return value.toString().replace(/[^0-9]/g, '')
                                        .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            }
                        JS))
                        ->stripCharacters(['.'])
                        ->minValue(0)
                        ->maxValue(9999999999)
                        ->helperText('Ejemplo: 1.000')
                        ->required(),

                    Forms\Components\TextInput::make('reference_lottery')
                        ->label('Lotería de Referencia')
                        ->maxLength(255)
                        ->required(),

                    Forms\Components\DatePicker::make('draw_date')
                        ->label('Fecha de Sorteo')
                        ->required(),

                    Forms\Components\Select::make('digits_count')
                        ->label('Cantidad de Cifras')
                        ->options([
                            2 => '2 Cifras',
                            3 => '3 Cifras',
                            4 => '4 Cifras'
                        ])
                        ->default(3)
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Estado')
                        ->options([                            
                            'OPEN' => '🟢 Abierta',
                            'CLOSED' => '🔴 Cerrada'
                        ])
                        ->default('OPEN')
                        ->required(),
                ])
                ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->label('Título'),                
                Tables\Columns\TextColumn::make('prize_type')->label('Tipo de Premio')                    
                    ->formatStateUsing(fn ($state) => $state == 1 ? '💵 Dinero' : '📦 Artículo'),
                Tables\Columns\TextColumn::make('jackpot_prize')->prefix('$')->label('Premio en pesos'),
                Tables\Columns\TextColumn::make('ticket_price')->prefix('$')->label('Precio'),
                Tables\Columns\TextColumn::make('reference_lottery')->label('Lotería'),
                Tables\Columns\TextColumn::make('draw_date')->date()->label('Fecha de Sorteo'),
                Tables\Columns\BadgeColumn::make('status')->label('Estado')                    
                    ->colors([
                        'success' => 'OPEN',
                        'danger' => 'CLOSED',
                    ])
                    ->formatStateUsing(fn ($state) => __($state)),
                // Contar tickets vendidos por rifa usando el método estático del modelo Ticket countTicketsByRaffle()
                Tables\Columns\TextColumn::make('tickets_sold')->label('Vendidos')
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver detalles')
                    ->tooltip('Ver detalles de la rifa, incluyendo números vendidos y pendientes')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->tooltip('Editar los detalles de la rifa')
                    ->icon('heroicon-m-pencil')
                    ->color('primary')
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->tooltip('Eliminar esta rifa de forma permanente')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->iconButton(),
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
            $oldPath = 'raffle-images/' . $record->image_path;
            Storage::disk('public')->delete($oldPath);
        }

        $record = parent::handleRecordUpdate($record, $data);

        return $record;
    }

    /**
     * Metodo para mutar los datos del formulario antes de guardarlos en la base de datos, específicamente para asegurarnos de que solo se guarde el nombre del archivo de la imagen y no la ruta completa.
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['image_path'])) {
            // Extraer solo el nombre del archivo si viene con ruta
            $data['image_path'] = basename($data['image_path']);
        }
        return $data;
    }

    // Este método se puede usar para mutar los datos del formulario antes de que se llenen en el formulario, pero en este caso no necesitamos hacer ninguna mutación específica, así que simplemente devolvemos los datos sin cambios.
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }
}
