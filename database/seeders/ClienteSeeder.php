<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        // Este seeder ahora funcionará porque las columnas 'nombre' y 'telefono' existen
        Cliente::create([
            'id' => 1,
            'nombre' => 'Cliente General',
            'telefono' => '000000000',
            'email' => 'cliente@general.com',
        ]);

        Cliente::factory(10)->create();
    }
}