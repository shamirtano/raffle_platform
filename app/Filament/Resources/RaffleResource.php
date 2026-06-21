<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RaffleResource\Pages;
use App\Filament\Resources\RaffleResource\RelationManagers;
use App\Models\Raffle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

class RaffleResource extends Resource
{
    protected static ?string $model = Raffle::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Rifa';
    protected static ?string $pluralModelLabel = 'Rifas';

    /**
     * Manejar los permisos de usuario para acceder a este recurso
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->can('manage-raffles') || $user->can('view-dashboard'));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Rifa')
                    ->schema([
                        Forms\Components\TextInput::make('title')->label('Título')->required()->maxLength(255),                        
                        Forms\Components\TextInput::make('slug')->label('Slug')->maxLength(255)->unique(ignoreRecord: true)->helperText('Se autogenera a partir del título si se deja en blanco.')->disabledOn('edit'),
                        Forms\Components\Textarea::make('description')->label('Descripción')->rows(3)->columnSpan(2),
                        Forms\Components\TextInput::make('jackpot_prize')->label('Premio en pesos')->numeric()->prefix('$')->required(),
                        Forms\Components\TextInput::make('ticket_price')->label('Precio de la boleta o ticket')->numeric()->prefix('$')->required(),
                        Forms\Components\TextInput::make('reference_lottery')->label('Lotería de Referencia')->required(),
                        Forms\Components\DatePicker::make('draw_date')->label('Fecha de Sorteo')->required(),
                        Forms\Components\Select::make('digits_count')->label('Cantidad de Cifras')
                            ->options([2 => '2 Cifras', 3 => '3 Cifras', 4 => '4 Cifras'])
                            ->default(3)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(['OPEN' => 'Abierta', 'CLOSED' => 'Cerrada'])
                            ->default('OPEN')
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->label('Título'),
                Tables\Columns\TextColumn::make('jackpot_prize')->money('COP')->prefix('$')->label('Premio en pesos'),
                Tables\Columns\TextColumn::make('ticket_price')->money('COP')->prefix('$')->label('Precio de la boleta o ticket'),
                Tables\Columns\TextColumn::make('draw_date')->date()->label('Fecha de Sorteo'),
                Tables\Columns\BadgeColumn::make('status')->label('Estado')                    
                    ->colors([
                        'success' => 'OPEN',
                        'danger' => 'CLOSED',
                    ])
                    ->formatStateUsing(fn ($state) => __($state)),
                Tables\Columns\TextColumn::make('tickets_count')
                    ->counts('tickets')
                    ->label('Boletas Vendidas'),
                
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'OPEN' => 'Abierta',
                        'CLOSED' => 'Cerrada',
                    ])
                    ->label('Estado'),
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
            'index' => Pages\ListRaffles::route('/'),
            'create' => Pages\CreateRaffle::route('/create'),
            'edit' => Pages\EditRaffle::route('/{record}/edit'),
        ];
    }
}
