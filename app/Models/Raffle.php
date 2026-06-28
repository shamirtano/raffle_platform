<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Raffle extends Model
{
    protected $table = 'raffles';

    protected $fillable = [
        'title', 'slug', 'description', 'image_path', 'organizer', 
        'contact_info', 'social_media_url', 'prize_type', 
        'jackpot_prize', 'ticket_price', 'reference_lottery', 
        'draw_date', 'game_type', 'digits_count', 'status'
    ];    

    protected $casts = [
        'draw_date' => 'date',
        'prize_type' => 'integer',
        'digits_count' => 'integer',
        'jackpot_prize' => 'decimal:2',
        'ticket_price' => 'decimal:2',
        'raffle_configurations' => 'array',
        'draw_date' => 'datetime',
    ];

    /**
     * Regla de Negocio Core: Valida si la cantidad de números comprados
     * coincide con los combos obligatorios definidos en la parametrización global.
     */
    public function validateOrderPackages(int $numbersCount): bool
    {
        $packageMultiples = RaffleConfiguration::getVal('package_multiples', []);
        
        if (empty($packageMultiples)) {
            return true; // Si no hay restricciones, permite la venta
        }

        // Convierte los valores a enteros por seguridad y busca concordancia
        return in_array($numbersCount, array_map('intval', $packageMultiples));
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticketOrders(): HasMany
    {
        return $this->hasMany(TicketOrder::class);
    }

    public function configuration(): HasOne
    {
        return $this->hasOne(RaffleConfiguration::class);
    }

    public function getAvailableNumbersCount(): int
    {
        $totalNumbers = pow(10, $this->digits_count);
        $soldNumbersCount = $this->soldTickets()->sum('ticket_numbers');

        $pendingNumbersCount = TicketOrder::where('raffle_id', $this->id)
            ->where('status', 'pending')
            ->get()
            ->flatMap(function ($order) {
                return $order->ticket_numbers ?? [];
            })->count();

        return $totalNumbers - $soldNumbersCount - $pendingNumbersCount;
    }
}
