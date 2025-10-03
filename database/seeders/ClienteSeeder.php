<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::create(['nombre' => 'Cliente General']);
        Cliente::factory(20)->create();
    }
}
