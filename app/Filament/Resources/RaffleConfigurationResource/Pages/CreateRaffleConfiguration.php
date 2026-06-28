<?php

namespace App\Filament\Resources\RaffleConfigurationResource\Pages;

use App\Filament\Resources\RaffleConfigurationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRaffleConfiguration extends CreateRecord
{
    protected static string $resource = RaffleConfigurationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
