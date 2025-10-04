<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ApartadoController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ProveedoresController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí es donde se registran todas las rutas de la API para tu aplicación.
| Laravel las carga automáticamente con el prefijo /api.
|
*/

// --- INVENTARIO Y CLIENTES ---
// El panel los busca en /api/inventario/...
Route::prefix('inventario')->group(function () {
    Route::get('/productos', [InventarioController::class, 'apiIndex']);
    Route::post('/productos', [InventarioController::class, 'apiStore']);
    Route::get('/clientes', [ClienteController::class, 'apiIndex']);
    Route::post('/clientes', [ClienteController::class, 'apiStore']);
});

// --- PROVEEDORES ---
// El panel los busca en /api/proveedores/...
Route::prefix('proveedores')->group(function () {
    Route::get('/', [ProveedoresController::class, 'apiIndex']);
    Route::post('/', [ProveedoresController::class, 'apiStore']);
    Route::get('/{id}', [ProveedoresController::class, 'apiShow']);
    Route::post('/{id}/facturas', [ProveedoresController::class, 'apiStoreFactura']);
});

// --- APARTADOS ---
// El panel los busca en /api/apartados
Route::get('/apartados', [ApartadoController::class, 'apiIndex']);
Route::post('/apartados', [ApartadoController::class, 'apiStore']);

// --- ESTADÍSTICAS ---
// El panel las busca en /api/estadisticas
Route::get('/estadisticas', [EstadisticasController::class, 'apiIndex']);

// --- VENTAS ---
// El panel las busca en /api/ventas/finalizar
Route::post('/ventas/finalizar', [VentaController::class, 'finalizarVentaApi']);

// Ruta de usuario de Sanctum (estándar de Laravel)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});