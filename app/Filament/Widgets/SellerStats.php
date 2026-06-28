<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class SellerStats extends BaseWidget
{
    protected function getStats(): array
    {
        // Solo para usuarios que NO son administradores
        if (Auth::user()->hasRole('admin')) {
            return [];
        }

        $userId = Auth::id();
        
        // Contar tickets del usuario creados hoy
        $ticketsHoy = Ticket::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        return [
            Stat::make('Mis Tickets Vendidos Hoy', $ticketsHoy)
                ->description('Total registrado en el sistema')
                ->descriptionIcon('heroicon-s-ticket')
                ->color('success'),
        ];
    }

    // Esto oculta el widget si el usuario es Admin
    public static function canView(): bool
    {
        return !Auth::user()->hasRole('admin');
    }
}
