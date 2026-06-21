<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Mensaje para WhatsApp
        $numero = preg_replace('/[^0-9]/', '', $record->customer_phone);
        $numerosList = implode(', ', $record->ticket_numbers ?? []);

        $mensaje = "🎟️ *¡Boleta o Ticket Creado Exitosamente!*\n\n" .
                   "Hola *{$record->customer_name}*,\n\n" .
                   "Tu boleta o ticket para *{$record->raffle->title}* ha sido generado.\n\n" .
                   "📌 *Números:* {$numerosList}\n\n" .
                   "¡Mucha suerte! 🍀✨";

        $whatsappUrl = "https://api.whatsapp.com/send?phone={$numero}&text=" . rawurlencode($mensaje);

        // SweetAlert con opción para enviar por WhatsApp
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
