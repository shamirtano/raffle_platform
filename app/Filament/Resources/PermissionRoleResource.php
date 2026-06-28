<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionRoleResource\Pages;
use App\Filament\Resources\PermissionRoleResource\RelationManagers;
use App\Models\PermissionRole;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Permission;

class PermissionRoleResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $navigationIcon = 'heroicon-s-key';
    protected static ?string $navigationGroup = 'Seguridad';
    protected static ?string $modelLabel = 'Permiso';
    protected static ?string $pluralModelLabel = 'Permisos';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detalles del Permiso')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Permiso')
                            ->placeholder('ej: sell_tickets, manage_users')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->required()
                            ->maxLength(255),

                        Select::make('roles')
                            ->label('Asignar a Roles')
                            ->multiple()
                            ->relationship('roles', 'display_name')
                            ->preload(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('display_name')->label('Permiso')->searchable(),
                Tables\Columns\TextColumn::make('guard_name')->label('Guard'),
                Tables\Columns\TextColumn::make('roles.display_name')
                    ->label('Roles Asociados')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'display_name')
                    ->label('Filtrar por Rol'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-s-eye')
                    ->tooltip('Ver Permiso')
                    ->iconButton()
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-s-pencil')
                    ->tooltip('Editar Permiso')
                    ->iconButton()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-s-trash')
                    ->tooltip('Eliminar Permiso')
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
            'index' => Pages\ListPermissionRoles::route('/'),
            'create' => Pages\CreatePermissionRole::route('/create'),
            'edit' => Pages\EditPermissionRole::route('/{record}/edit'),
        ];
    }
}
