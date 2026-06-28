<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinancierResource\Pages;
use App\Filament\Resources\FinancierResource\RelationManagers;
use App\Models\Financier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FinancierResource extends Resource
{
    protected static ?string $model = Financier::class;
    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';
    protected static ?string $navigationGroup = 'Finanzas y Contabilidad';
    protected static ?string $modelLabel = 'Inversionista';
    protected static ?string $pluralModelLabel = 'Inversionistas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del Inversionista')
                    ->columnSpan('full')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Tipo de Inversionista')
                    ->required()
                    ->options([
                        'OWNER' => 'Dueño',
                        'INDIVIDUAL' => 'Individual',
                        'COMPANY' => 'Empresa',
                        'OTHER' => 'Otro',
                    ]),
                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('participation_percentage')
                    ->label('Porcentaje de Participación')
                    ->required()
                    ->suffix('%')
                    ->placeholder('0.00')
                    ->numeric(),
                Forms\Components\TextInput::make('capital_contributed')                    
                    ->label('Capital Contribuido')
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
                    ->maxValue(999999999999)
                    ->helperText('Ejemplo: 500.000.000')
                    ->required(),            
                Forms\Components\Toggle::make('is_active')
                    ->label('¿Activo?')                    
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre del Inversionista')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de Inversionista')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'OWNER' => 'Dueño',
                        'INDIVIDUAL' => 'Individual',
                        'COMPANY' => 'Empresa',
                        'OTHER' => 'Otro',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->searchable(),
                Tables\Columns\TextColumn::make('participation_percentage')
                    ->label('% Participación')
                    ->suffix('%')
                    ->alignCenter()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capital_contributed')
                    ->label('Capital Contribuido')
                    ->prefix('$')
                    ->alignRight()                    
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('¿Activo?')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Inversionista')
                    ->options([
                        'OWNER' => 'Dueño',
                        'INDIVIDUAL' => 'Individual',
                        'COMPANY' => 'Empresa',
                        'OTHER' => 'Otro',
                    ]),
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('¿Activo?')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver detalles')
                    ->tooltip('Ver detalles del inversionista seleccionado')
                    ->icon('heroicon-s-eye')
                    ->color('info')
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->tooltip('Editar información del inversionista seleccionado')
                    ->icon('heroicon-s-pencil')
                    ->color('warning')
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->tooltip('Eliminar el inversionista seleccionado')
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
            'index' => Pages\ListFinanciers::route('/'),
            'create' => Pages\CreateFinancier::route('/create'),
            'edit' => Pages\EditFinancier::route('/{record}/edit'),
        ];
    }
}
