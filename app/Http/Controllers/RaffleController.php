<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\Ticket;
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
        
        // Obtener números vendidos para esta rifa
        $soldTickets = Ticket::where('raffle_id', $raffle->id)
            ->pluck('ticket_number')
            ->toArray();

        // Calcular el rango total (Ej: 3 cifras = 1000 números, de 000 a 999)
        $totalNumbers = pow(10, $raffle->digits_count);

        return view('raffles.show', compact('raffle', 'soldTickets', 'totalNumbers'));
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
