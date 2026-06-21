<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
