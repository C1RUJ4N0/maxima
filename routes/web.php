<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\RegistroVentasController; 
use App\Http\Controllers\FacturaController; 
use App\Http\Controllers\InventarioController; 
use App\Http\Controllers\ApartadoController; 
use App\Http\Controllers\VentaController; 

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function() {
    Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class,'login']);
    Route::get('/register', [RegisterController::class,'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class,'register']);
    Route::get('/password/reset', [ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class,'reset'])->name('password.update');
});

Route::middleware('auth')->group(function() {
    Route::post('/logout', [LoginController::class,'logout'])->name('logout');
    
    // Ruta principal del Panel TPV
    Route::get('/panel', [PanelController::class,'index'])->name('panel.index');
    
    // RUTAS WEB (VISTAS) - (Otras vistas que no son el TPV)
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas');
    Route::get('/proveedores', [ProveedoresController::class, 'index'])->name('proveedores'); 
    Route::get('/apartados', [ApartadoController::class, 'index'])->name('apartados.index'); 
    Route::get('/registro-ventas', [RegistroVentasController::class, 'index'])->name('registroventas.index');
    
    // CRUD WEB (Formularios tradicionales)
    Route::put('/facturas/{factura}', [FacturaController::class, 'update'])->name('facturas.update'); // Web
    Route::post('/proveedores', [ProveedoresController::class, 'store'])->name('proveedores.store');
    Route::put('/proveedores/{proveedor}', [ProveedoresController::class, 'update'])->name('proveedores.update');
    Route::delete('/proveedores/{proveedor}', [ProveedoresController::class, 'destroy'])->name('proveedores.destroy');
    Route::post('/apartados', [ApartadoController::class, 'store'])->name('apartados.store'); 
    Route::put('/apartados/{apartado}', [ApartadoController::class, 'update'])->name('apartados.update'); 
    Route::delete('/apartados/{apartado}', [ApartadoController::class, 'destroy'])->name('apartados.destroy'); 

    // RUTAS API INTERNAS (AJAX / TPV)
    Route::prefix('api')->group(function () {
        
        // Inventario/Productos/Clientes API (CRUD y listado)
        Route::get('/inventario', [InventarioController::class, 'apiIndex']);
        Route::post('/inventario', [InventarioController::class, 'storeProducto']); 
        Route::put('/inventario/{producto}', [InventarioController::class, 'apiUpdateProducto']);
        Route::delete('/inventario/{producto}', [InventarioController::class, 'apiDestroyProducto']);
        Route::get('/inventario/clientes', [InventarioController::class, 'getClientes']);
        Route::post('/inventario/clientes', [InventarioController::class, 'storeCliente']);
        Route::get('/inventario/productos', [InventarioController::class, 'searchProductos']);
        
        // Ventas (TPV)
        Route::get('/ventas', [VentaController::class, 'apiIndex']);
        Route::post('/ventas', [VentaController::class, 'store']);
        Route::put('/ventas/{venta}', [VentaController::class, 'apiUpdate']);
        Route::delete('/ventas/{venta}', [VentaController::class, 'apiDestroy']);
        
        // Estadísticas
        Route::get('/estadisticas', [EstadisticasController::class, 'apiIndex']);
        
        // Proveedores API
        Route::get('/proveedores', [ProveedoresController::class, 'apiIndex']);
        Route::post('/proveedores', [ProveedoresController::class, 'apiStore']);
        Route::get('/proveedores/{proveedor}', [ProveedoresController::class, 'apiShow']);
        Route::post('/proveedores/{proveedor}/facturas', [ProveedoresController::class, 'apiStoreFactura']);
        Route::put('/proveedores/{proveedor}', [ProveedoresController::class, 'apiUpdate']);
        Route::delete('/proveedores/{proveedor}', [ProveedoresController::class, 'apiDestroy']);
        
        // Apartados API
        Route::get('/apartados', [ApartadoController::class, 'apiIndex']);
        Route::post('/apartados', [ApartadoController::class, 'apiStore']);
        Route::put('/apartados/{apartado}', [ApartadoController::class, 'apiUpdate']);
        Route::delete('/apartados/{apartado}', [ApartadoController::class, 'apiDestroy']);
        
        // Facturas API
        Route::put('/facturas/{factura}', [FacturaController::class, 'apiUpdate']);
        Route::delete('/facturas/{factura}', [FacturaController::class, 'apiDestroy']);
    });
});