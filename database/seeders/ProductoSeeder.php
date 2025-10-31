<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['nombre' => 'Lavandina Sedile 1lt', 'precio' => 780, 'existencias' => 15],
            ['nombre' => 'Lavandina Sedile 2lt', 'precio' => 1499, 'existencias' => 15],
            ['nombre' => 'Lavandina Sedile 3lt', 'precio' => 3199, 'existencias' => 15],
            ['nombre' => 'Lavandina Nikito 5lt', 'precio' => 5999, 'existencias' => 5],
            ['nombre' => 'Lavandina Ayudín 1lt', 'precio' => 1200, 'existencias' => 15],
            ['nombre' => 'Lavandina Ayudín 2lt', 'precio' => 2400, 'existencias' => 15],
            ['nombre' => 'Detergente Ala Limón 300ml', 'precio' => 2200, 'existencias' => 20],
            ['nombre' => 'Detergente Ala Limón 500ml', 'precio' => 2650, 'existencias' => 20],
            ['nombre' => 'Detergente Cif Limón 300ml', 'precio' => 2350, 'existencias' => 20],
            ['nombre' => 'Detergente Cif Limón 500ml', 'precio' => 2950, 'existencias' => 20],
            ['nombre' => 'Detergente Magistral Limón 500ml', 'precio' => 3750, 'existencias' => 20],
            ['nombre' => 'Detergente Magistral Limón 750ml', 'precio' => 4750, 'existencias' => 20],
            ['nombre' => 'Detergente Magistral Limón 1.4lt', 'precio' => 9999, 'existencias' => 5],
            ['nombre' => 'Desinfectante Poett Lavanda 900ml', 'precio' => 2800, 'existencias' => 10],
            ['nombre' => 'Desinfectante Poett Primavera 900ml', 'precio' => 2800, 'existencias' => 10],
            ['nombre' => 'Desinfectante Poett Bebe 1.8lt', 'precio' => 4999, 'existencias' => 5],
            ['nombre' => 'Limpia Pisos Mr. Músculo Floral 900ml', 'precio' => 3200, 'existencias' => 10],
            ['nombre' => 'Limpia Pisos Mr. Músculo Lavanda 1.8lt', 'precio' => 5600, 'existencias' => 5],
            ['nombre' => 'Limpiavidrios Ayudín 500ml', 'precio' => 3100, 'existencias' => 10],
            ['nombre' => 'Limpiavidrios Glassex 500ml', 'precio' => 3300, 'existencias' => 10],
            ['nombre' => 'Jabón en Pan Federal Blanco 400g', 'precio' => 950, 'existencias' => 25],
            ['nombre' => 'Jabón en Pan Federal Azul 400g', 'precio' => 950, 'existencias' => 25],
            ['nombre' => 'Jabón en Pan Zorro 400g', 'precio' => 850, 'existencias' => 20],
            ['nombre' => 'Jabón en Pan Ala 400g', 'precio' => 1100, 'existencias' => 15],
            ['nombre' => 'Suavizante Vivere Cielo Azul 900ml', 'precio' => 3600, 'existencias' => 10],
            ['nombre' => 'Suavizante Vivere Sensaciones 1.8lt', 'precio' => 6200, 'existencias' => 8],
            ['nombre' => 'Suavizante Comfort Jazmín 900ml', 'precio' => 3500, 'existencias' => 10],
            ['nombre' => 'Suavizante Comfort Jazmín 1.8lt', 'precio' => 5900, 'existencias' => 8],
            ['nombre' => 'Desodorante de Ambientes Poett Vainilla 360ml', 'precio' => 2700, 'existencias' => 12],
            ['nombre' => 'Desodorante de Ambientes Poett Lavanda 360ml', 'precio' => 2700, 'existencias' => 12],
            ['nombre' => 'Desodorante de Ambientes Glade Manzana Canela 360ml', 'precio' => 3100, 'existencias' => 12],
            ['nombre' => 'Desodorante de Ambientes Glade Brisa Marina 360ml', 'precio' => 3100, 'existencias' => 12],
            ['nombre' => 'Limpiador Cif Crema Original 450ml', 'precio' => 4200, 'existencias' => 10],
            ['nombre' => 'Limpiador Cif Crema Limón 450ml', 'precio' => 4200, 'existencias' => 10],
            ['nombre' => 'Limpiador Cif Antigrasa Spray 500ml', 'precio' => 4800, 'existencias' => 8],
            ['nombre' => 'Limpiador Mr. Músculo Cocina 500ml', 'precio' => 4900, 'existencias' => 8],
            ['nombre' => 'Limpiador Mr. Músculo Baño 500ml', 'precio' => 4900, 'existencias' => 8],
            ['nombre' => 'Limpiador Ayudín Multiuso 500ml', 'precio' => 4500, 'existencias' => 8],
            ['nombre' => 'Escoba Plástica Vassoura Roja', 'precio' => 2800, 'existencias' => 10],
            ['nombre' => 'Escoba Plástica Vassoura Azul', 'precio' => 2800, 'existencias' => 10],
            ['nombre' => 'Secador de Piso Vassoura 45cm', 'precio' => 3100, 'existencias' => 10],
            ['nombre' => 'Trapo de Piso Amarillo', 'precio' => 900, 'existencias' => 30],
            ['nombre' => 'Trapo de Piso Blanco', 'precio' => 900, 'existencias' => 30],
            ['nombre' => 'Esponja Virulana Clásica x2', 'precio' => 1100, 'existencias' => 25],
            ['nombre' => 'Esponja Virulana Acero x2', 'precio' => 1200, 'existencias' => 25],
            ['nombre' => 'Guantes de Látex Amarillos Talle M', 'precio' => 1600, 'existencias' => 15],
            ['nombre' => 'Guantes de Látex Amarillos Talle L', 'precio' => 1600, 'existencias' => 15],
            ['nombre' => 'Bolsa de Residuos Negra 60x90 30u', 'precio' => 4800, 'existencias' => 10],
            ['nombre' => 'Bolsa de Residuos Verde 80x110 30u', 'precio' => 5200, 'existencias' => 10],
            ['nombre' => 'Paño Multiuso Trapito 3un', 'precio' => 1300, 'existencias' => 20],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}
