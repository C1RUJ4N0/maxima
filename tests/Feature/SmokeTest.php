<?php

test('rutas publicas cargan correctamente', function ($url) {
    $this->get($url)->assertOk();
})->with([
    '/login',
    '/register',
]);

test('rutas protegidas redirigen al login', function ($url) {
    $this->get($url)
         ->assertStatus(302)
         ->assertRedirect('/login');
})->with([
    '/',
    '/panel',
    '/inventario',
    '/clientes',    // <--- Vuelve a agregarlo
    '/proveedores',
    '/ventas',      // <--- Vuelve a agregarlo
    '/apartados',
    '/estadisticas',
    '/registro-ventas',
]);