<?php

namespace Database\Seeders;

use App\Models\RaffleConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RaffleConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tipos de juegos permitidos en la plataforma
        RaffleConfiguration::updateOrCreate(
            ['key' => 'raffle_types'],
            [
                'display_name' => 'Tipos de Rifas Permitidas',
                'value' => ['traditional' => 'Tradicional Colombiana', 'baloto' => 'Tipo Baloto (Rangos)'],
                'description' => 'Estructuras de juego soportadas por el sistema.',
                'type' => 'json',
                'is_active' => true,
            ]
        );

        // 2. Múltiplos o Paquetes de venta obligatorios
        RaffleConfiguration::updateOrCreate(
            ['key' => 'package_multiples'],
            [
                'display_name' => 'Paquetes de Venta (Múltiplos)',
                'value' => [5, 10, 20, 50],
                'description' => 'Cantidad de boletas obligatorias por paquete comercial.',
                'type' => 'array',
                'is_active' => true,
            ]
        );

        // 3. Loterías autorizadas de Colombia
        RaffleConfiguration::updateOrCreate(
            ['key' => 'allowed_lotteries'],
            [
                'display_name' => 'Loterías de Referencia',
                'value' => [
                    'medellin' => 'Lotería de Medellín',
                    'bogota' => 'Lotería de Bogotá',
                    'boyaca' => 'Lotería de Boyacá',
                    'cundinamarca' => 'Lotería de Cundinamarca',
                    'santander' => 'Lotería de Santander',
                    'valle' => 'Lotería del Valle',
                    'tolima' => 'Lotería del Tolima',                    
                    'manizales' => 'Lotería de Manizales',
                ],
                'description' => 'Operadores de sorteos nacionales válidos.',
                'type' => 'json',
                'is_active' => true,
            ]
        );

        // 4. Configuración de la cantidad máxima de boletas por usuario
        RaffleConfiguration::updateOrCreate(
            ['key' => 'max_tickets_per_user'],
            [
                'display_name' => 'Máximo de Boletas por Usuario',
                'value' => 100,
                'description' => 'Cantidad máxima de boletas que un usuario puede comprar en una rifa.',
                'type' => 'integer',
                'is_active' => true,
            ]
        );

        // 5. Configuración de la cantidad mínima de boletas por usuario
        RaffleConfiguration::updateOrCreate(
            ['key' => 'min_tickets_per_user'],
            [
                'display_name' => 'Mínimo de Boletas por Usuario',
                'value' => 1,
                'description' => 'Cantidad mínima de boletas que un usuario debe comprar en una rifa.',
                'type' => 'integer',
                'is_active' => true,
            ]
        );

        // 6. Cantidad de cifras por juego (para validar la longitud de los números)
        RaffleConfiguration::updateOrCreate(
            ['key' => 'digits_options'],
            [
                'display_name' => 'Cantidad de Cifras por Juego',
                'value' => [2, 3, 4],
                'description' => 'Número de cifras que debe tener cada número de boleta.',
                'type' => 'json',
                'is_active' => true,
            ]
        );
    }
}
