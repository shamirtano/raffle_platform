<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    protected $table = 'raffles';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_path',
        'organizer',
        'contact_info',
        'social_media_url',
        'prize_type',
        'jackpot_prize',
        'ticket_price',
        'reference_lottery',
        'draw_date',
        'digits_count',
        'status',
    ];

    // Casts
    protected $casts = [
        'draw_date' => 'date',
        'prize_type' => 'integer',
        'jackpot_prize' => 'decimal:2',
        'ticket_price' => 'decimal:2',
    ];

    // Relación con Tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Relación con TicketOrders
    public function ticketOrders()
    {
        return $this->hasMany(TicketOrder::class);
    }
}
