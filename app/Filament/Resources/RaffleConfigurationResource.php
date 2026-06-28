<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RaffleConfigurationResource\Pages;
use App\Models\RaffleConfiguration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\KeyValue;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class RaffleConfigurationResource extends Resource
{
    protected static ?string $model = RaffleConfiguration::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Rifas y Sorteos';
    protected static ?string $modelLabel = 'Parametrización';
    protected static ?int $navigationSort = 4;
    protected static ?string $pluralModelLabel = 'Parametrización';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('⚙️ Ajustes del Parámetro Global')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('key')
                                ->label('Clave del Sistema (Key)')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->disabledOn('edit') // Evita romper el código al editar la llave
                                ->maxLength(255),

                            TextInput::make('display_name')
                                ->label('Nombre Visible (Español)')
                                ->required()
                                ->maxLength(255),
                        ]),

                        TextInput::make('description')
                            ->label('Descripción de la Variable')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Grid::make(2)->schema([
                            Select::make('type')
                                ->label('Tipo de Dato')
                                ->options([
                                    'string' => 'Texto (String)',
                                    'integer' => 'Número (Integer)',
                                    'boolean' => 'Booleano (Verdadero/Falso)',
                                    'json' => 'Diccionario (JSON)',
                                    'array' => 'Lista (Array)',
                                ])
                                ->disabled()
                                ->live(),

                            Toggle::make('is_active')
                                ->label('Parámetro Activo')
                                ->default(true)
                                ->inline(false),
                        ]),
                    ]),                 

                Section::make('📦 Valor de la Configuración')
                    ->description('Define el contenido de la variable basándote en su tipo de dato.')
                    ->schema([
                        
                        // 1. String -> Guardado en 'value_string'
                        TextInput::make('value_string')
                            ->label('Valor de la Variable')                                            
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'string'),

                        // 2. Integer -> Guardado en 'value_integer'
                        TextInput::make('value_integer')
                            ->label('Valor Numérico')
                            ->numeric()
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'integer'),

                        // 3. Boolean -> Guardado en 'value_boolean'
                        Toggle::make('value_boolean')
                            ->label('¿Activar esta opción?')
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('type') === 'boolean'),

                        // 4. JSON -> Guardado en 'value_json'
                        KeyValue::make('value_json')
                            ->label('Elementos del JSON (Clave => Valor)')
                            ->keyLabel('Clave / Índice')
                            ->valueLabel('Valor / Opción')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'json'),
                        
                        // 5. Array -> Guardado en 'value_array'
                        TagsInput::make('value_array')
                            ->label('Lista de Valores (Array)')
                            ->placeholder('Agregar un valor...')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'array')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Parámetro')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key')
                    ->label('Clave Interna')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),

                // Muestra un resumen del JSON o valor de forma limpia
                TextColumn::make('value')
                    ->label('Valor Actual')
                    ->limit(40)
                    ->formatStateUsing(function ($state, RaffleConfiguration $record) {
                        if ($record->type === 'json' && is_array($state)) {
                            return implode(', ', array_map(
                                fn($k, $v) => "$k: $v", 
                                array_keys($state), 
                                $state
                            ));
                        }
                        if ($record->type === 'boolean') {
                            return $state ? 'Verdadero' : 'Falso';
                        }
                        return $state;
                    }),

                TextColumn::make('is_active')
                    ->label('Estado')
                    ->formatStateUsing(fn ($state) => $state ? 'Activo' : 'Inactivo')
                    ->badge(fn ($state) => $state ? 'success' : 'danger')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ])
                    ->label('Filtrar por Tipo'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->tooltip('Ver Detalles de Configuración')
                    ->iconButton()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->tooltip('Editar Configuración')
                    ->iconButton()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Eliminar Configuración')
                    ->iconButton()
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->actionsColumnLabel('Acciones');
            
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRaffleConfigurations::route('/'),            
            'edit' => Pages\EditRaffleConfiguration::route('/{record}/edit'),
        ];
    }
}