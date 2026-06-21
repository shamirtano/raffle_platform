<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OthersUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea un usuario socio y 2 vendedores adicionales de pruebas
        $partner = User::create([
            'first_name' => 'Socio',
            'last_name' => 'Prueba',
            'email' => 'socio@elpalomonegro.com',
            'password' => bcrypt('password'),
        ]);

        $partner->assignRole('partner');

        $seller1 = User::create([
            'first_name' => 'Vendedor1',
            'last_name' => 'Prueba',
            'email' => 'vendedor1@elpalomonegro.com',
            'password' => bcrypt('password'),
        ]);

        $seller1->assignRole('seller');

        $seller2 = User::create([
            'first_name' => 'Vendedor2',
            'last_name' => 'Prueba',
            'email' => 'vendedor2@elpalomonegro.com',
            'password' => bcrypt('password'),
        ]);

        $seller2->assignRole('seller');
    }
}
