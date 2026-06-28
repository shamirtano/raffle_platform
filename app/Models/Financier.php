<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Financier extends Model
{
    protected $fillable = [
        'name',
        'type',
        'email',
        'participation_percentage',
        'capital_contributed',
        'is_active',
    ];

    protected $casts = [
        'participation_percentage' => 'decimal:2',
        'capital_contributed' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
