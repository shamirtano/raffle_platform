<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
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
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'area' => 'required|in:RESTAURANT,POOL,FAMILY_ZONE,EVENT_HALL',
            'reservation_time' => 'required|date|after:now',
            'guests_count' => 'required|integer|min:1|max:50',
        ]);

        Reservation::create($request->all());

        // Notificar al usuario por whtasapp automáticamente desde el backend y redigigir a la pagina principal o landingpage
        
        
        return redirect()->route('home')->with('success', '¡Reserva realizada con éxito! Nos pondremos en contacto contigo pronto.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}
