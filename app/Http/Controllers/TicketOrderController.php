<?php

namespace App\Http\Controllers;

use App\Models\TicketOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketOrderController extends Controller
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
            'ticket_numbers' => 'required|string',
        ]);

        $numbersArray = array_map('trim', explode(',', $request->ticket_numbers));

        // Validación de concurrencia unificada
        $takenNumbers = DB::table('tickets')
            ->where('raffle_id', $request->raffle_id)
            ->get()
            ->flatMap(fn($t) => json_decode($t->ticket_numbers, true)['numbers'] ?? json_decode($t->ticket_numbers, true) ?? [])
            ->merge(
                DB::table('ticket_orders')
                    ->where('raffle_id', $request->raffle_id)
                    ->where('status', 'pending')
                    ->get()
                    ->flatMap(fn($o) => json_decode($o->ticket_numbers, true) ?? [])
            )->toArray();

        foreach ($numbersArray as $num) {
            if (in_array($num, $takenNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => "El número {$num} ya se encuentra apartado o vendido."
                ], 422);
            }
        }

        // Crear registro en la tabla ticket_orders
        TicketOrder::create([
            'raffle_id' => $request->raffle_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'ticket_numbers' => $numbersArray,
            'status' => 'pending'
        ]);

        // Retorna éxito en JSON para disparar el Swal.fire de éxito
        return response()->json([
            'success' => true,
            'message' => 'Pedido registrado correctamente.'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(TicketOrder $ticketOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TicketOrder $ticketOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketOrder $ticketOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketOrder $ticketOrder)
    {
        //
    }
}
