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
    
    public static function countTicketsByRaffle()
    {
        return self::select('raffle_id', DB::raw('SUM(JSON_LENGTH(ticket_numbers->"$.numbers")) as total_tickets'))
            ->groupBy('raffle_id')
            ->get();
    }

    public function raffle()
    {
        return $this->belongsTo(Raffle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
