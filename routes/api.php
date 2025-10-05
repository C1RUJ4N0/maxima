<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\ApartadoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// RUTA PARA FINALIZAR LA VENTA DESDE EL PANEL
Route::post('/ventas', [VentaController::class, 'store']);

// RUTA PARA OBTENER LAS ESTADÍSTICAS EN EL PANEL
Route::get('/estadisticas', [EstadisticasController::class, 'apiIndex']);

// TUS OTRAS RUTAS DE LA API (EXISTENTES EN TU PANEL)
Route::get('/inventario/clientes', [InventarioController::class, 'getClientes']);
Route::post('/inventario/clientes', [InventarioController::class, 'storeCliente']);
Route::get('/inventario/productos', [InventarioController::class, 'searchProductos']);
Route::post('/inventario/productos', [InventarioController::class, 'storeProducto']);

Route::get('/proveedores', [ProveedoresController::class, 'apiIndex']);
Route::post('/proveedores', [ProveedoresController::class, 'apiStore']);
Route::get('/proveedores/{proveedor}', [ProveedoresController::class, 'apiShow']);
Route::post('/proveedores/{proveedor}/facturas', [ProveedoresController::class, 'apiStoreFactura']);

Route::get('/apartados', [ApartadoController::class, 'apiIndex']);
Route::post('/apartados', [ApartadoController::class, 'apiStore']);