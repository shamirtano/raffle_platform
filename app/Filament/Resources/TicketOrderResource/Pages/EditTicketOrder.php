<?php

namespace App\Filament\Resources\TicketOrderResource\Pages;

use App\Filament\Resources\TicketOrderResource;
use App\Models\Ticket;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditTicketOrder extends EditRecord
{
    protected static string $resource = TicketOrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        // SI ASESOR CAMBIÓ EL ESTADO A PROCESADO, DISPARAMOS LA MIGRACIÓN Y LA ALERTA
        if ($record->status === 'processed') {
            
            // 1. Validar si ya existe el ticket definitivo para no duplicarlo
            $ticketExists = Ticket::where('raffle_id', $record->raffle_id)                
                ->whereJsonContains('ticket_numbers', $record->ticket_numbers)
                ->exists();

            if (!$ticketExists) {
                DB::transaction(function () use ($record) {
                    Ticket::create([
                        'raffle_id' => $record->raffle_id,
                        'user_id' => auth()->id(),
                        'customer_name' => $record->customer_name,
                        'customer_phone' => $record->customer_phone,
                        'ticket_numbers' => $record->ticket_numbers,
                        'payment_status' => $record->payment_status === 'PAID' ? 'PAID' : 'PENDING',
                    ]);
                });
            }
    
            // 2. Preparar la URL de WhatsApp
            $numero = preg_replace('/[^0-9]/', '', $record->customer_phone);
            if (strlen($numero) === 10 && str_starts_with($numero, '3')) {
                $numero = '57' . $numero;
            }

            $numerosList = implode(', ', $record->ticket_numbers);
            $urlResultados = "https://tudominio.com/resultados"; 
            
            $mensaje = <<<WHATSAPP
                🎉 *¡Felicidades {$record->customer_name}!* 🎉

                🍀 ¡Bienvenido/a a *El Palomo Negro*! 🍀

                Estás participando en la rifa:

                🔥 *{$record->raffle->title}* 🔥

                🎟️ *Tus números de suerte:*
                {$numerosList}

                Estado del pago: *PAGADO*

                Puedes realizar tu pagos mediante Nequi, Bancolombia o en Efectivo en nuestras oficinas.

                📲 *Revisa los resultados aquí:*
                👉 {$urlResultados}

                ✨ ¡Que la suerte te acompañe y cruces los dedos! ✨

                ¿Tienes alguna duda? Escríbenos 😊
                WHATSAPP;

            $textEncoded = rawurlencode($mensaje);
            $whatsappUrl = "https://api.whatsapp.com/send?phone={$numero}&text={$textEncoded}";

            // 3. Mostrar la notificación flotante (Actúa como tu Swal de confirmación)
            Notification::make()
                ->title('🎉 ¡Pedido Guardado y Procesado!')
                ->body('¿Deseas enviar la notificación oficial al cliente por WhatsApp en este momento?')
                ->success()
                ->persistent()
                ->actions([
                    // Usamos Action::make() directamente gracias al nuevo namespace importado
                    Action::make('send_whatsapp')
                        ->label('Sí, Enviar WhatsApp')
                        ->icon('heroicon-m-chat-bubble-left-right')
                        ->color('success')
                        ->url($whatsappUrl)
                        ->openUrlInNewTab()
                        ->close(), // En v3 se usa ->close() para cerrar la notificación al hacer clic
                    
                    Action::make('cancel')
                        ->label('No, después')
                        ->color('secondary')
                        ->close(),
                ])
                ->send();
        }
    }
}
