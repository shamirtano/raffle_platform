<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'area',
        'reservation_time',
        'guests_count',
        'additional_notes',
        'status',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
        'guests_count' => 'integer',
    ];
}
