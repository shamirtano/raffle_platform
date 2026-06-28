<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaffleConfiguration extends Model
{
    protected $fillable = [
        'key', 
        'display_name', 
        'value', 
        'description', 
        'type', 
        'is_active'
    ];

    protected $casts = [
        'value' => 'array', // 🌟 Cast automático de JSON a Array de PHP
        'is_active' => 'boolean',
    ];

    /**
     * Helper para obtener el valor de cualquier configuración rápidamente
     */
    public static function getVal(string $key, $default = null)
    {
        $config = self::where('key', $key)->where('is_active', true)->first();
        return $config ? $config->value : $default;
    }
}
