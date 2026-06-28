<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilamentAdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Primero verificamos si está autenticado
        if (!auth()->check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        // 2. Luego verificamos si tiene el rol de admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'No tienes acceso autorizado para esta área.');
        }

        return $next($request);
    }
}
