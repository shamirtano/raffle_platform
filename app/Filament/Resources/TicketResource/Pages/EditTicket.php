<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['ticket_numbers'])) {
            $numbers = $data['ticket_numbers'];

            // 🟢 Convertimos a array puro si Laravel lo devolvió como objeto/colección
            if (is_object($numbers)) {
                $numbers = json_decode(json_encode($numbers), true);
            } elseif (is_string($numbers)) {
                $numbers = json_decode($numbers, true);
            }

            // Extraemos el array plano interno para el TagsInput
            if (is_array($numbers) && isset($numbers['numbers'])) {
                $data['ticket_numbers'] = array_values($numbers['numbers']);
            } elseif (is_array($numbers)) {
                $data['ticket_numbers'] = array_values($numbers);
            } else {
                $data['ticket_numbers'] = [];
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['ticket_numbers'])) {
            $numbers = $data['ticket_numbers'];

            if (is_object($numbers)) {
                $numbers = json_decode(json_encode($numbers), true);
            }

            if (is_array($numbers) && isset($numbers['numbers'])) {
                $numbers = $numbers['numbers'];
            }

            // Guardamos estrictamente con la estructura JSON esperada
            $data['ticket_numbers'] = ['numbers' => array_values((array)$numbers)];
        }

        return $data;
    }
}