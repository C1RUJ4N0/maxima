<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\ApartadoController;


// --- INVENTARIO Y VENTAS (Endpoints para el panel de Alpine.js) ---
Route::get('/inventory/productos', [InventarioController::class, 'apiIndex']);
Route::post('/inventory/productos', [InventarioController::class, 'apiStore']);
Route::get('/inventory/clientes', [ClienteController::class, 'apiIndex']);
Route::post('/proveedores', [ProveedoresController::class, 'apiStore']);
Route::post('/inventory/clientes', [ClienteController::class, 'apiStore']);
Route::post('/inventory/ventas/finalizar', [VentaController::class, 'finalizarVentaApi']);

// --- MÓDULOS ADICIONALES (Endpoints para el panel de Alpine.js) ---
Route::get('/estadisticas', [EstadisticasController::class, 'apiIndex']);
Route::get('/proveedores', [ProveedoresController::class, 'apiIndex']);
Route::get('/apartados', [ApartadoController::class, 'apiIndex']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});