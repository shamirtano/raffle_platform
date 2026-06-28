<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $table = 'landing_settings';

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    public static function getVal(string $key, $default = null)
    {
        $config = self::where('key', $key)->first();
        return $config ? $config->value : $default;
    }

    /**
     * Traduce las llaves técnicas a nombres amigables para el panel administrativo
     */
    public function getLabelAttribute(): string
    {
        return match ($this->key) {
            'landing_business_info'     => '🏢 Información e Identificación del Negocio',    
            'landing_reservation_areas' => '🪑 Zonas y Áreas de Reserva',            
            'landing_hero_section'      => '✨ Banner Principal (Hero)',
            'landing_featured_menu'     => '🍽️ Menú Gastronómico Destacado',
            'landing_events_gallery'    => '🎉 Cartelera de Eventos y Shows',
            'landing_opening_hours'     => '🕒 Horarios de Atención',
            'landing_social_links'      => '📱 Enlaces de Redes Sociales',
            'landing_gallery'           => '📸 Galería de Imágenes',
            'landing_business_info'     => '🏢 Información del Negocio',
            'landing_seo_tags'          => '🔍 Configuración SEO (Google)',
            'landing_pixel_ids'         => '📊 Píxeles y Marketing',
            default                     => ucwords(str_replace(['landing_', '_'], ['', ' '], $this->key)),
        };
    }
}
