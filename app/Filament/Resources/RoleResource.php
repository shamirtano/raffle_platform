<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Infolists\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-s-shield-check';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Rol';
    protected static ?string $pluralModelLabel = 'Roles';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles del Rol')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Interno del Rol')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->maxLength(100),

                        TextInput::make('display_name')
                            ->label('Nombre del Rol')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->disabledOn('edit')
                            ->required()
                            ->maxLength(50),

                        Textarea::make('description')
                            ->label('Descripción del Rol')
                            ->rows(4)
                            ->placeholder('Descripción breve del rol')
                            ->helperText('Máximo 250 caracteres.')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                    ])->columns(3),

                Section::make('Asignar Permisos Directos')
                    ->description('Selecciona los permisos que pertenecen a este rol.')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Permisos Disponibles')
                            ->relationship('permissions', 'display_name')
                            ->options(function () {
                                return Permission::pluck('display_name', 'id')->toArray();
                            })
                            ->bulkToggleable()
                            ->columns(3),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('Nombre Interno'),
                Tables\Columns\TextColumn::make('display_name')->label('Nombre del Rol'),
                Tables\Columns\TextColumn::make('description')->label('Descripción')->limit(50),                
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Cant. Permisos')
                    ->counts('permissions')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->label('Fecha de Creación')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Creado Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Creado Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->tooltip('Ver Detalles del Rol')
                    ->icon('heroicon-s-eye')
                    ->iconButton()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->tooltip('Editar Rol')
                    ->icon('heroicon-s-pencil')
                    ->iconButton()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Eliminar Rol')
                    ->icon('heroicon-s-trash')
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información General') // 🟢 Cambiado a Section
                    ->schema([
                        TextEntry::make('display_name')->label('Nombre del Rol'),
                        TextEntry::make('name')->label('Nombre Interno'),
                        TextEntry::make('guard_name')->label('Guard'),
                        TextEntry::make('description')->label('Descripción')->columnSpanFull(),
                    ])->columns(3),

                Section::make('Permisos Otorgados')
                    ->description('Lista de accesos asociados a este rol.')
                    ->schema([                        
                        TextEntry::make('permissions.display_name')
                            ->label('Permisos Asociados')
                            ->badge()
                            ->color('success')
                            ->placeholder('Este rol no cuenta con permisos asignados.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}