<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        // Usuario Super Administrador del sistema - Desarrolladores y administradores de alto nivel
        $super_user = User::create([
            'first_name' => 'Super',
            'last_name'  => 'Administrador',
            'email'      => 'super@elpalomonegro.com',
            'password'   => Hash::make('password'),
            'status'     => 'active',
        ]);

        $super_user->assignRole('superadmin');

        // Usuario Administrador o propietario del sistema
        $user = User::create([
            'first_name' => 'Admin',
            'last_name'  => 'Principal',
            'email'      => 'admin@elpalomonegro.com',
            'password'   => Hash::make('password'),
            'status'     => 'active',
        ]);

        $user->assignRole('admin');
    }
}
