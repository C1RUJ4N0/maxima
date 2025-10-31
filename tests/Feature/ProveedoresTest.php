<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Proveedor; // <--- Importante para el assertDatabaseMissing

uses(RefreshDatabase::class);

test('un usuario no administrador no puede crear un proveedor', function () {
    $user = User::factory()->create(['role' => 'user']); 

    $datosProveedor = [
        'nombre' => 'Proveedor de Prueba',
        'telefono' => '123456789',
    ];

    $this->actingAs($user)
         ->post(route('proveedores.store'), $datosProveedor)
         ->assertStatus(302); // <--- ARREGLO 1: Esperamos 302 (Redirección), no 403

    // Añadimos esta verificación: aseguramos que no se creó nada
    $this->assertDatabaseMissing('Proveedor', $datosProveedor); // <--- ARREGLO 2: Usamos el nombre de tabla 'Proveedor'
});

test('un administrador puede crear un proveedor', function () {
    $admin = User::factory()->create(['role' => 'admin']); 

    $datosProveedor = [
        'nombre' => 'Coca-Cola',
        'telefono' => '987654321',
        'domicilio' => 'Calle Falsa 123',
    ];

    $this->actingAs($admin)
         ->post(route('proveedores.store'), $datosProveedor)
         ->assertStatus(302); 

    // ARREGLO 2: Usar el nombre de tabla correcto 'Proveedor'
    $this->assertDatabaseHas('Proveedor', [ 
        'nombre' => 'Coca-Cola',
        'telefono' => '987654321',
    ]);
});

test('el nombre del proveedor es requerido', function () {
    $admin = User::factory()->create(['role' => 'admin']); 

    $datosInvalidos = [
        'nombre' => '',
        'telefono' => '123',
    ];

    $this->actingAs($admin)
         ->post(route('proveedores.store'), $datosInvalidos)
         ->assertInvalid(['nombre' => 'required']);
});