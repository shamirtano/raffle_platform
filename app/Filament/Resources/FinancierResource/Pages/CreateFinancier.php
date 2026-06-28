<?php

namespace App\Filament\Resources\FinancierResource\Pages;

use App\Filament\Resources\FinancierResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancier extends CreateRecord
{
    protected static string $resource = FinancierResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
