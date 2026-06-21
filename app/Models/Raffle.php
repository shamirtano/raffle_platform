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
        'jackpot_prize',
        'ticket_price',
        'reference_lottery',
        'draw_date',
        'digits_count',
        'status',
    ];

    // Relación con Tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
