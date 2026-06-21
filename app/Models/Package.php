<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['name', 'quantity', 'price'];

    // Relación: Un paquete puede tener muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
