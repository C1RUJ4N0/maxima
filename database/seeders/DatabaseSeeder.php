<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@maxima.com',
        ]);

        User::factory(10)->create();

        $this->call([
            ProductoSeeder::class,
            ClienteSeeder::class,
        ]);
    }
}
