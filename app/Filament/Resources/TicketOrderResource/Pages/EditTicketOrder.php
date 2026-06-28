<?php

namespace App\Filament\Resources\TicketOrderResource\Pages;

use App\Filament\Resources\TicketOrderResource;
use App\Models\Ticket;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class EditTicketOrder extends EditRecord
{
    protected static string $resource = TicketOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * 🌟 INTERCEPTOR DE GUARDADO: 
     * Se ejecuta justo antes de guardar los cambios del formulario en la BD.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Guardamos el estado original antes de la actualización
        $originalStatus = $this->record->status;

        // Si el pedido pasa de 'pending' a 'processed', disparamos la creación de tickets reales
        if ($originalStatus === 'pending' && $data['status'] === 'processed') {
            
            DB::transaction(function () {
                // 1. Obtener los números solicitados (vienen como array desde el TagsInput)
                $numbers = is_array($this->record->ticket_numbers) 
                    ? $this->record->ticket_numbers 
                    : json_decode($this->record->ticket_numbers, true) ?? [];

                if (empty($numbers)) {
                    throw new \Exception("El pedido no contiene números de ticket válidos.");
                }

                // 2. Crear el Ticket definitivo en la base de datos
                Ticket::create([
                    'raffle_id'      => $this->record->raffle_id,
                    'user_id'        => auth()->id(), // El asesor que procesa la venta
                    'customer_name'  => $this->record->customer_name,
                    'customer_phone' => $this->record->customer_phone,
                    'ticket_numbers' => ['numbers' => $numbers], // Estructura JSON estándar
                    'payment_status' => 'PENDING', // Al procesarse de forma manual, se asume pendiente se cambia en la lista de tickets
                ]);
            });

            // Enviar notificación flotante de éxito al asesor
            Notification::make()
                ->title('¡Pedido Procesado Exitosamente!')
                ->body("Se han generado los boletos oficiales para {$this->record->customer_name}.")
                ->success()
                ->send();
        }

        return $data;
    }

    /**
     * Redirección tras guardar los cambios
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}