<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Asegúrate de que estos controladores existan en app/Http/Controllers/
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EstadisticasController;

/*
|--------------------------------------------------------------------------
| Rutas API del Sistema TPV (Autenticadas)
|--------------------------------------------------------------------------
| Todas las rutas dentro de este grupo requieren un token de autenticación.
*/

// Ruta de prueba para el usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Agrupación de todas las rutas del TPV, protegidas por autenticación
Route::middleware(['auth:sanctum'])->group(function () {
    
    // --- Rutas de INVENTARIO y CLIENTES (Usando InventoryController) ---
    
    // 1. Productos
    // GET /api/productos -> Obtiene todos los productos del inventario
    Route::get('/productos', [InventoryController::class, 'obtenerProductos']);
    // POST /api/productos -> Agrega un nuevo producto
    Route::post('/productos', [InventoryController::class, 'agregarProducto']);
    
    // 2. Clientes
    // GET /api/clientes -> Obtiene la lista de clientes
    Route::get('/clientes', [InventoryController::class, 'obtenerClientes']);

    // 3. Venta (Finalización de Transacción)
    // POST /api/ventas/finalizar -> Procesa la venta, actualiza stock y registra la transacción
    Route::post('/ventas/finalizar', [InventoryController::class, 'finalizarVenta']);

    // --- Rutas de DASHBOARD (Usando VentaController y EstadisticasController) ---

    // 4. Estadísticas
    // GET /api/estadisticas -> Datos para el dashboard (p.ej., resumen de ventas)
    Route::get('/estadisticas', [EstadisticasController::class, 'index']);
    
    // 5. Apartados (VentaController, asumiendo que maneja lógica de apartados)
    // GET /api/apartados -> Listado principal de apartados
    Route::get('/apartados', [VentaController::class, 'obtenerApartados']);
    // GET /api/apartados-vencer -> Listado de apartados cercanos a vencer
    Route::get('/apartados-vencer', [VentaController::class, 'apartadosPorVencer']);
    
});
