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
}
