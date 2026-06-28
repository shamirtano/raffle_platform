<?php

namespace App\Filament\Resources\FinancierResource\Pages;

use App\Filament\Resources\FinancierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancier extends EditRecord
{
    protected static string $resource = FinancierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
