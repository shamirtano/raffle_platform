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
        $user = User::create([
            'first_name' => 'Admin',
            'last_name'  => 'Principal',
            'email'      => 'admin@elpalomonegro.com',
            'password'   => Hash::make('password'),
            'status'     => 'active',
        ]);

        // Asignar el rol de admin que creamos antes
        $user->assignRole('admin');
    }
}
