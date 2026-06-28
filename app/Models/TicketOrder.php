<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class TicketOrder extends Model
{
    protected $fillable = [
        'raffle_id',
        'customer_name',
        'customer_phone',
        'ticket_numbers',
        'status', // pending, processed, cancelled
        'user_id', // ID del usuario que proceso la venta, para trazabilidad
    ];

    protected $casts = [
        'ticket_numbers' => 'array',
    ];

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    protected static function booted()
    {
        static::creating(function (TicketOrder $order) {
            $raffle = Raffle::find($order->raffle_id);
            
            if ($raffle) {
                // Contamos los números dentro del array JSON del pedido web/manual
                $numbersCount = is_array($order->ticket_numbers) 
                    ? count($order->ticket_numbers) 
                    : count($order->ticket_numbers['numbers'] ?? []);

                if (!$raffle->validateOrderPackages($numbersCount)) {
                    throw ValidationException::withMessages([
                        'ticket_numbers' => 'La cantidad de números no corresponde a ningún paquete o combo comercial activo.',
                    ]);
                }
            }
        });
    }
}
