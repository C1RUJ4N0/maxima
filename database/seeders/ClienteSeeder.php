<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        
        Cliente::firstOrCreate(
            ['nombre' => 'Cliente General'],
            [
                'telefono' => '0000000000',
                'email' => 'cliente@general.com'
            ]
        );
    }
}