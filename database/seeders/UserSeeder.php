<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'empleado',
            'email' => 'empleado@example.com',
            'password' => bcrypt('password')
        ]);
    }
}