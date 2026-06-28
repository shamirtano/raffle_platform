<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        if (isset($data['ticket_numbers']['numbers'])) {
            $data['ticket_numbers'] = ['numbers' => array_values($data['ticket_numbers']['numbers'])];
        } elseif (isset($data['ticket_numbers']) && is_array($data['ticket_numbers'])) {
            $data['ticket_numbers'] = ['numbers' => array_values($data['ticket_numbers'])];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $numbersArray = $record->ticket_numbers['numbers'] ?? $record->ticket_numbers ?? [];
        $numerosList = implode(', ', $numbersArray);

        $numero = preg_replace('/[^0-9]/', '', $record->customer_phone);
        if (strlen($numero) === 10 && str_starts_with($numero, '3')) {
            $numero = '57' . $numero;
        }

        $mensaje = "🎟️ *¡Boleta o Ticket Creado Exitosamente!*\n\n" .
                   "Hola *{$record->customer_name}*,\n\n" .
                   "Tu boleta o ticket para *{$record->raffle->title}* ha sido generado.\n\n" .
                   "📌 *Números:* {$numerosList}\n\n" .
                   "¡Mucha suerte! 🍀✨";

        $whatsappUrl = "https://api.whatsapp.com/send?phone={$numero}&text=" . rawurlencode($mensaje);

        $this->js(<<<JS
            Swal.fire({
                title: '¡Boleta o Ticket creada correctamente!',
                text: '¿Deseas enviar la boleta o ticket por WhatsApp ahora?',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Sí, Enviar por WhatsApp',
                cancelButtonText: 'No, Gracias',
                confirmButtonColor: '#22c55e',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('{$whatsappUrl}', '_blank');
                }
            });
        JS);
    }
}