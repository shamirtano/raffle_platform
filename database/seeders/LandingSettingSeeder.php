<?php

namespace Database\Seeders;

use App\Models\LandingSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Identificación e Info del Negocio (PRIMERO)
        LandingSetting::updateOrCreate(
            ['key' => 'landing_business_info'],
            ['value' => [                
                'address' => 'Km 12 Vía Campestre, Medellín, Colombia',
                'email' => 'contacto@palomonegro.com',                
                'maps_iframe' => 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d31595.14375677164!2d-76.0720316!3d8.1631043!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e5af50004060adb%3A0x8dc3aaded8ac3d9a!2sRestaurante%20Campestre%20El%20Palomo%20Negro!5e0!3m2!1ses-419!2sco!4v1782624524660!5m2!1ses-419!2sco',
            ]]
        );

        // 2. Reservas
        LandingSetting::updateOrCreate(
            ['key' => 'landing_reservation_areas'],
            ['value' => [
                'general' => 'Zona General / Tablado',
                'vip' => 'Palcos / Zona VIP',
                'barra' => 'Barra Principal'
            ]]
        );
        

        // 3. Hero Principal
        LandingSetting::updateOrCreate(
            ['key' => 'landing_hero_section'],
            ['value' => [
                'title' => 'El Palomo Negro',
                'subtitle' => 'Restaurante Bar - El mejor ambiente, licores y gastronomía',
                'main_image' => null,
                'video_url' => 'https://www.youtube.com/watch?v=ejemplo'
            ]]
        );

        // 4. Menú Destacado (Gastronomía y Bebidas)
        LandingSetting::updateOrCreate(
            ['key' => 'landing_featured_menu'],
            ['value' => [
                [
                    'name' => 'Cortes de Carne a la Parrilla',
                    'description' => 'Especialidad de la casa con el toque El Palomo Negro.',
                    'price' => '45000',
                    'image' => null
                ],
                [
                    'name' => 'Cócteles Autor y Licores',
                    'description' => 'Una amplia selección para acompañar tus noches.',
                    'price' => '28000',
                    'image' => null
                ]
            ]]
        );

        // 5. Próximos Eventos y Conciertos
        LandingSetting::updateOrCreate(
            ['key' => 'landing_events_gallery'],
            ['value' => [
                [
                    'title' => 'Noche de Despecho y Parranda',
                    'date' => 'Cada Fin de Semana',
                    'description' => 'Artistas en vivo y el mejor ambiente de la región.',
                    'image' => null
                ]
            ]]
        );

        // 6. Horarios de Atención
        LandingSetting::updateOrCreate(
            ['key' => 'landing_opening_hours'],
            ['value' => [
                'jueves_viernes' => 'Jueves y Viernes: 4:00 PM - 2:00 AM',
                'sabados' => 'Sábados: 12:00 PM - 3:00 AM',
                'domingos_festivos' => 'Domingos y Festivos: 12:00 PM - 12:00 AM'
            ]]
        );

        // 7. Redes Sociales
        LandingSetting::updateOrCreate(
            ['key' => 'landing_social_links'],
            ['value' => [
                'facebook' => 'https://www.facebook.com/p/El-Palomo-NEGRO-61583288660689/',
                'instagram' => 'https://instagram.com/elpalomonegro',
                'whatsapp' => '573000000000' // Solo el número para facilitar los links directos wa.me
            ]]
        );

        // 8. Galería de Fotos del Establecimiento (Múltiples Imágenes)
        LandingSetting::updateOrCreate(
            ['key' => 'landing_gallery'],
            ['value' => []]
        );

        // 9. Configuración SEO para Google
        LandingSetting::updateOrCreate(
            ['key' => 'landing_seo_tags'],
            ['value' => [
                'meta_title' => 'El Palomo Negro | Restaurante Bar Campestre',
                'meta_description' => 'Disfruta de la mejor gastronomía criolla asada, licores premium y eventos en vivo en nuestro espacio campestre exclusivo.',
                'meta_keywords' => 'restaurante campestre, bar medellin, carnes a la parrilla, eventos en vivo, el palomo negro'
            ]]
        );

        // 10. Scripts de Seguimiento y Marketing (Pixeles)
        LandingSetting::updateOrCreate(
            ['key' => 'landing_pixel_ids'],
            ['value' => [
                'google_analytics_id' => null,
                'facebook_pixel_id' => null,
                'tiktok_pixel_id' => null
            ]]
        );
    }
}
