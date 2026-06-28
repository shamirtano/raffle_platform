<?php

namespace App\Filament\Resources\RaffleConfigurationResource\Pages;

use App\Filament\Resources\RaffleConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRaffleConfigurations extends ListRecords
{
    protected static string $resource = RaffleConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Nueva Configuración de Rifa')
                ->icon('heroicon-s-plus')
                ->button()
                ->color('primary'),
        ];
    }
}
