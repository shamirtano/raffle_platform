<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\RaffleCombo;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaffleComboController extends Controller
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
            'purchase_type' => 'required|in:manual,combo',
            'ticket_number' => 'required_if:purchase_type,manual|nullable|string',
            'raffle_combo_id' => 'required_if:purchase_type,combo|nullable|exists:raffle_combos,id',
        ]);

        $raffle = Raffle::findOrFail($request->raffle_id);

        // ==========================================
        // MODALIDAD 1: PARTICIPACIÓN MANUAL
        // ==========================================
        if ($request->purchase_type === 'manual') {
            $ticketNumber = str_pad($request->ticket_number, $raffle->digits_count, '0', STR_PAD_LEFT);

            $exists = Ticket::where('raffle_id', $request->raffle_id)
                ->where('ticket_number', $ticketNumber)
                ->exists();

            if ($exists) {
                return back()->with('toast_error', '¡El número ' . $ticketNumber . ' ya no está disponible!');
            }

            Ticket::create([
                'raffle_id' => $request->raffle_id,
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'ticket_number' => $ticketNumber,
                'payment_status' => 'PENDING',
            ]);

            return back()->with('swal_success', 'Tu participación con el número ' . $ticketNumber . ' se registró exitosamente.');
        }

        // ==========================================
        // MODALIDAD 2: COMPRA ALEATORIA POR COMBO
        // ==========================================
        if ($request->purchase_type === 'combo') {
            $combo = RaffleCombo::findOrFail($request->raffle_combo_id);
            
            // 1. Calcular el total de números posibles según las cifras de la rifa (Ej: 3 cifras = 1000 números)
            $totalNumbersCount = pow(10, $raffle->digits_count);
            
            // 2. Obtener los números que YA están vendidos
            $soldTickets = Ticket::where('raffle_id', $raffle->id)
                ->pluck('ticket_number')
                ->toArray();

            // 3. Generar la bolsa de números disponibles de forma virtual
            $availableNumbers = [];
            for ($i = 0; $i < $totalNumbersCount; $i++) {
                $numStr = str_pad($i, $raffle->digits_count, '0', STR_PAD_LEFT);
                if (!in_array($numStr, $soldTickets)) {
                    $availableNumbers[] = $numStr;
                }
            }

            // 4. Validar si hay suficientes números libres para el combo
            if (count($availableNumbers) < $combo->tickets_count) {
                return back()->with('toast_error', 'No hay suficientes números libres en la tómbola para este combo.');
            }

            // 5. Selección aleatoria (Mezclar bolsa y tomar la cantidad del combo)
            shuffle($availableNumbers);
            $selectedNumbers = array_slice($availableNumbers, 0, $combo->tickets_count);

            // 6. Registro masivo de los tickets asignados
            foreach ($selectedNumbers as $number) {
                Ticket::create([
                    'raffle_id' => $raffle->id,
                    'user_id' => Auth::id(),
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'ticket_number' => $number,
                    'payment_status' => 'PENDING',
                ]);
            }

            return back()->with('swal_success', '¡Combo asignado! Has adquirido los siguientes ' . $combo->tickets_count . ' números aleatorios: ' . implode(', ', $selectedNumbers));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RaffleCombo $raffleCombo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RaffleCombo $raffleCombo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RaffleCombo $raffleCombo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RaffleCombo $raffleCombo)
    {
        //
    }
}
