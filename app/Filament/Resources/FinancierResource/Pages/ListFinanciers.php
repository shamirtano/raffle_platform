<?php

namespace App\Filament\Resources\FinancierResource\Pages;

use App\Filament\Resources\FinancierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinanciers extends ListRecords
{
    protected static string $resource = FinancierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Nuevo Inversionista')
                ->icon('heroicon-s-plus')
                ->button()
                ->color('primary'),            
        ];
    }
}
