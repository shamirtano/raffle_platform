<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = [
        'raffle_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'ticket_numbers',
        'payment_status',
    ];

    protected $casts = [
        'ticket_numbers' => 'array',
    ];

    // Contar tikets vendidos por rifa desglosando el array de ticket_numbers
    public static function countTicketsByRaffle()
    {
        return self::select('raffle_id', DB::raw('SUM(JSON_LENGTH(ticket_numbers)) as total_tickets'))
            ->groupBy('raffle_id')
            ->get();
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
