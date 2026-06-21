<?php

namespace App\Filament\Widgets;

use App\Models\Raffle;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Rifas Activas', Raffle::where('status', 'OPEN')->count()),
            Stat::make('Tickets Vendidos', Ticket::count()),
            Stat::make('Ingresos Totales', '$' . Ticket::where('payment_status', 'PAID')->count() * 10), // Ejemplo
        ];
    }

    // Solo visible para quien tiene permiso de ver finanzas o reportes
    public static function canView(): bool
    {
        return auth()->user()->can('view-dashboard');
    }
}
