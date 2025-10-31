<?php

use Illuminate\Foundation\Testing\RefreshDatabase; // <-- ASEGÚRATE DE QUE ESTO ESTÉ IMPORTADO
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Aplicamos TestCase (para cargar Laravel) y RefreshDatabase (para las
| migraciones) a AMBAS carpetas: 'Feature' y 'Unit'.
|
*/

// Carga Laravel Y las migraciones para las pruebas 'Feature'
pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class) // <-- ESTA LÍNEA ES CRUCIAL PARA FEATURE
    ->in('Feature');

// Carga Laravel Y las migraciones para las pruebas 'Unit'
pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class) // <-- ESTA LÍNEA ES CRUCIAL PARA UNIT
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}