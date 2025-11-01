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

// Rutas de Invitados
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

// Rutas para TODOS los usuarios autenticados
Route::middleware('auth')->group(function() {
    Route::post('/logout', [LoginController::class,'logout'])->name('logout');
    
    // --- VISTAS WEB (Visibles para todos) ---
    Route::get('/panel', [PanelController::class,'index'])->name('panel.index');
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas');
    Route::get('/proveedores', [ProveedoresController::class, 'index'])->name('proveedores'); 
    Route::get('/apartados', [ApartadoController::class, 'index'])->name('apartados.index'); 
    Route::get('/registro-ventas', [RegistroVentasController::class, 'index'])->name('registroventas.index');

    // --- API RUTAS (Lectura - Visibles para todos) ---
    Route::prefix('api')->group(function () {
        // Inventario/Productos/Clientes (Lectura)
        Route::get('/inventario', [InventarioController::class, 'apiIndex']);
        Route::get('/inventario/clientes', [InventarioController::class, 'getClientes']);
        Route::get('/inventario/productos', [InventarioController::class, 'searchProductos']);
        
        // Ventas (Lectura)
        Route::get('/ventas', [VentaController::class, 'apiIndex']);
        
        // Estadísticas (Lectura)
        Route::get('/estadisticas', [EstadisticasController::class, 'apiIndex']);
        
        // Proveedores (Lectura)
        Route::get('/proveedores', [ProveedoresController::class, 'apiIndex']);
        Route::get('/proveedores/{proveedor}', [ProveedoresController::class, 'apiShow']);
        
        // Apartados (Lectura)
        Route::get('/apartados', [ApartadoController::class, 'apiIndex']);

        // --- RUTAS DE CREACIÓN PARA 'USER' ---
        Route::post('/inventario/clientes', [InventarioController::class, 'storeCliente']);
        Route::post('/apartados', [ApartadoController::class, 'apiStore']);
        Route::post('/ventas', [VentaController::class, 'store']);
    });

    // -----------------------------------------------------------------
    // --- ACCIONES DE ADMIN (Crear, Editar, Eliminar) ---
    // Aquí usamos tu middleware 'admin'
    // -----------------------------------------------------------------
    Route::middleware('admin')->group(function () {
    
        // CRUD WEB (Formularios tradicionales)
        Route::put('/facturas/{factura}', [FacturaController::class, 'update'])->name('facturas.update'); // Web
        Route::post('/proveedores', [ProveedoresController::class, 'store'])->name('proveedores.store');
        Route::put('/proveedores/{proveedor}', [ProveedoresController::class, 'update'])->name('proveedores.update');
        Route::delete('/proveedores/{proveedor}', [ProveedoresController::class, 'destroy'])->name('proveedores.destroy');
        Route::post('/apartados', [ApartadoController::class, 'store'])->name('apartados.store'); // <-- Esta es WEB, la de API está arriba
        Route::put('/apartados/{apartado}', [ApartadoController::class, 'update'])->name('apartados.update'); 
        Route::delete('/apartados/{apartado}', [ApartadoController::class, 'destroy'])->name('apartados.destroy'); 

        // RUTAS API (Solo acciones de ADMIN)
        Route::prefix('api')->group(function () {
            
            // Inventario/Productos (Crear, Editar, Borrar)
            Route::post('/inventario', [InventarioController::class, 'storeProducto']); 
            Route::put('/inventario/{producto}', [InventarioController::class, 'apiUpdateProducto']);
            Route::delete('/inventario/{producto}', [InventarioController::class, 'apiDestroyProducto']);
            
            // Ventas (Editar, Borrar)
            Route::put('/ventas/{venta}', [VentaController::class, 'apiUpdate']);
            Route::delete('/ventas/{venta}', [VentaController::class, 'apiDestroy']);
            
            // Proveedores (Crear, Editar, Borrar)
            Route::post('/proveedores', [ProveedoresController::class, 'apiStore']);
            Route::post('/proveedores/{proveedor}/facturas', [ProveedoresController::class, 'apiStoreFactura']);
            Route::put('/proveedores/{proveedor}', [ProveedoresController::class, 'apiUpdate']);
            Route::delete('/proveedores/{proveedor}', [ProveedoresController::class, 'apiDestroy']);
            
            // Apartados (Editar, Borrar)
            Route::put('/apartados/{apartado}', [ApartadoController::class, 'apiUpdate']);
            Route::delete('/apartados/{apartado}', [ApartadoController::class, 'apiDestroy']);
            
            // Facturas (Editar, Borrar)
            Route::put('/facturas/{factura}', [FacturaController::class, 'apiUpdate']);
            Route::delete('/facturas/{factura}', [FacturaController::class, 'apiDestroy']);
        });
    
    });
    // --- FIN: RUTAS SÓLO PARA ADMINISTRADORES ---
    
});