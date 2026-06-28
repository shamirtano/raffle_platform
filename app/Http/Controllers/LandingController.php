<?php

namespace App\Http\Controllers;

use App\Models\LandingSetting;
use App\Models\Raffle;
use App\Models\RaffleConfiguration;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index', [
            // Datos Estructurados para los partials
            'hero'         => LandingSetting::getVal('landing_hero_section'),
            'menu'         => LandingSetting::getVal('landing_featured_menu', []),
            'events'       => LandingSetting::getVal('landing_events_gallery', []),
            'gallery'      => LandingSetting::getVal('landing_gallery', []),
            'hours'        => LandingSetting::getVal('landing_opening_hours', []),
            'socials'      => LandingSetting::getVal('landing_social_links', []),
            'areas'        => LandingSetting::getVal('landing_reservation_areas', []),
            'business_info'=> LandingSetting::getVal('landing_business_info', []),
            'seo_tags'     => LandingSetting::getVal('landing_seo_tags', []),
            'pixel_ids'    => LandingSetting::getVal('landing_pixel_ids', []),

            'activeRaffles' => Raffle::where('status', 'OPEN')->get(),
            'packageMultiples' => RaffleConfiguration::getVal('package_multiples', [1, 5, 10, 20]),
        ]);
    }

    /**
     * Metodo que recibe los pedidos realizados por el cliente y notifica por whatsapp al cliente de la reserva
     * Almacena la solicitud de compra de tickets en una tabla de pedidos y le notifica a los pvendedores sobra las solicitudes
     * @param pedido_id
     */
    public function processOrder($pedido_id)
    {
        // Lógica para procesar el pedido, enviar notificaciones, etc.
    }
}
