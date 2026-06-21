<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
