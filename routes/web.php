<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RaffleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('home');

// RAFFLE ROUTES - Publicly accessible
Route::get('/raffles', [RaffleController::class, 'index'])->name('raffles.index');
Route::get('/raffles/{id}', [RaffleController::class, 'show'])->name('raffles.show');
Route::post('/raffles/order', [TicketOrderController::class, 'store'])->name('ticket-orders.store')->middleware('web');

Route::middleware('auth')->group(function () {
    // PROFILE ROUTES
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // RAFFLE ROUTES - Private Access
    Route::get('/raffles/create', [RaffleController::class, 'create'])->name('raffles.create');
    Route::post('/raffles/store', [RaffleController::class, 'store'])->name('raffles.store');
    Route::get('/raffles/{id}/edit', [RaffleController::class, 'edit'])->name('raffles.edit');
    Route::patch('/raffles/{id}', [RaffleController::class, 'update'])->name('raffles.update');
    Route::delete('/raffles/{id}', [RaffleController::class, 'destroy'])->name('raffles.destroy');        

    // TICKET ROUTES
    Route::post('/tickets/store', [TicketController::class, 'store'])->name('tickets.store');

    // RESERVATION ROUTES
    Route::post('/reservations/store', [ReservationController::class, 'store'])->name('reservations.store');
});