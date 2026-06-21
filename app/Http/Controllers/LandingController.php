<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use Illuminate\Http\Request;
use App\Models\RaffleCombo;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index', [
            'activeRaffles' => Raffle::where('status', 'OPEN')->get(),
            'activeCombos' => RaffleCombo::where('is_active', true)->get()
        ]);
    }

    /**
     * Metodo que recibe los pedidos realizados por el cliente y notifica por whatsapp al cliente de la reserva
     * Almacena la solicitud de compra de tickets en una tabla de pedidos y le notifica a los pvendedores sobra las solicitudes
     * @param pedido_id
     */
    public function processOrder($pedido_id)
    {
        // Lógica para procesar el pedido, enviar notificaciones, etc.
    }
}
