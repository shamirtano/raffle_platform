<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gestión de Sistema';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';

    /**
     * Controlar el acceso a este recurso basado en permisos
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->can('manage-users'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos Personales')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')->required(),
                        Forms\Components\TextInput::make('last_name')->required(),
                        Forms\Components\TextInput::make('email')->email()->required(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])->columns(2),

                Forms\Components\Section::make('Roles y Permisos')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {        
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('last_name')->label('Apellido')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('mobile_number')
                    ->label('Celular')
                    ->formatStateUsing(fn ($state) => $state ? "(+57){$state}" : '-')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->label('Activo')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TagsColumn::make('roles.display_name')->label('Roles'),
            ])
            ->filters([
                // Filtro para encontrar rápidamente vendedores
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'display_name'),
            ])
            ->actions([
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
