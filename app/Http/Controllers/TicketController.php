<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'raffle_id' => 'required|exists:raffles,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'ticket_number' => 'required|string',
        ]);

        // Verificar si el número ya fue vendido para esa rifa
        $exists = Ticket::where('raffle_id', $request->raffle_id)
            ->where('ticket_number', $request->ticket_number)
            ->exists();

        if ($exists) {
            return back()->with('toast_error', '¡El número seleccionado ya no está disponible!');
        }

        Ticket::create([
            'raffle_id' => $request->raffle_id,
            'user_id' => Auth::id(), // ID del socio autenticado
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'ticket_number' => $request->ticket_number,
            'payment_status' => $request->payment_status ?? 'PENDING',
        ]);

        return back()->with('swal_success', 'La venta del número ' . $request->ticket_number . ' se registró exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
