<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Producto;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->words(3, true),
            'existencias' => fake()->numberBetween(10, 100),
            'precio' => fake()->randomFloat(2, 500, 5000),
        ];
    }
}
