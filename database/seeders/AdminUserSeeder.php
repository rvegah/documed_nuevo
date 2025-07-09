<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@documed.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'active' => true,
            'email_verified_at' => now(),
        ]);

        // Crear usuario normal de prueba
        User::create([
            'name' => 'Usuario Demo',
            'email' => 'usuario@demo.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'active' => true,
            'email_verified_at' => now(),
        ]);
    }
}