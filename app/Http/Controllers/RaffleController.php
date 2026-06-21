<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\TicketOrder;
use Illuminate\Http\Request;

class RaffleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeRaffles = Raffle::where('status', 'OPEN')->get();

        return view('raffles.index', compact('activeRaffles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $raffle = Raffle::findOrFail($id);

        // 1. Números Procesados / Vendidos -> Color Rojo
        $soldTickets = Ticket::where('raffle_id', $raffle->id)            
            ->get()
            ->flatMap(function ($ticket) {
                // Si guardas estructurado como {"numbers": [...]} extraes la key, si es array directo dejas $ticket->ticket_numbers
                $data = $ticket->ticket_numbers;
                return isset($data['numbers']) ? $data['numbers'] : $data;
            })->toArray();

        $soldTicketsPaid = Ticket::where('raffle_id', $raffle->id)
            ->where('payment_status', 'PAID')
            ->get()
            ->flatMap(function ($ticket) {
                $data = $ticket->ticket_numbers;
                return isset($data['numbers']) ? $data['numbers'] : $data;
            })->toArray();

        // 2. Números en Pedidos Pendientes -> Color Gris
        $pendingTickets = TicketOrder::where('raffle_id', $raffle->id)
            ->where('status', 'pending')
            ->get()
            ->flatMap(function ($order) {
                return $order->ticket_numbers ?? [];
            })->toArray();

        $totalNumbers = pow(10, $raffle->digits_count);
        $soldNumbersCount = count($soldTickets);
        $availableNumbersCount = $totalNumbers - $soldNumbersCount - count($pendingTickets);

        return view('raffles.show', compact(
            'raffle',
            'soldTickets',
            'soldTicketsPaid',
            'pendingTickets',
            'totalNumbers',
            'soldNumbersCount',
            'availableNumbersCount'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Raffle $raffle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Raffle $raffle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Raffle $raffle)
    {
        //
    }
}
