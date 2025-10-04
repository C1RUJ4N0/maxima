<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Asegura que el "Cliente general" con ID 1 exista.
        Cliente::firstOrCreate(
            ['nombre' => 'Cliente general'],
            [
                'telefono' => '0000000000',
                'email' => 'cliente@general.com'
            ]
        );
    }
}