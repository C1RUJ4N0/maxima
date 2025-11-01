<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // --- ESTA ES LA CORRECCIÓN ---
        // Usamos firstOrCreate() para evitar el error de "Duplicate entry".
        // Esto buscará un usuario con el email 'admin@maxima.com'.
        // Si no lo encuentra, lo creará. Si ya existe, no hará nada.
        User::firstOrCreate(
            ['email' => 'admin@maxima.com'], // El campo único que debe buscar
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // Asegúrate de que esta sea la contraseña que quieres
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Si tienes otros usuarios (como 'empleado'), puedes añadirlos de la misma forma:
        /*
        User::firstOrCreate(
            ['email' => 'empleado@maxima.com'],
            [
                'name' => 'Empleado',
                'password' => Hash::make('password'),
                'role' => 'empleado',
                'email_verified_at' => now(),
            ]
        );
        */
    }
}
