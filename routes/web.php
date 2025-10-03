<?php

use Illuminate\Support\Facades\Route;
// Importamos los controladores de autenticación
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Importamos los controladores del sistema de Punto de Venta (POS) con nombres en español
use App\Http\Controllers\PanelController; // <-- USAMOS PanelController
use App\Http\Controllers\EstadisticasController; 
use App\Http\Controllers\ApartadoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProveedoresController;


Route::get('/', function () {
    return redirect()->route('login');
});

// --- GRUPO: Rutas para Usuarios NO AUTENTICADOS (guest) ---
Route::middleware('guest')->group(function() {
    
    // Autenticación (Login y Registro)
    Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class,'login']);
    Route::get('/register', [RegisterController::class,'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class,'register']);

    // Recuperación de Contraseña
    Route::get('/password/reset', [ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class,'reset'])->name('password.update');
});


// --- GRUPO: Rutas para Usuarios AUTENTICADOS (auth) ---
Route::middleware('auth')->group(function() {
    
    // Cierre de Sesión
    Route::post('/logout', [LoginController::class,'logout'])->name('logout');

    // Panel / Punto de Venta
    // CORRECCIÓN: La ruta ahora se llama 'panel.index' para coincidir con la plantilla app.blade.php
    Route::get('/panel', [PanelController::class,'index'])->name('panel.index'); 

    // Módulos del Carrito y Venta (Usando PanelController para la lógica del POS)
    // Estas rutas de POST deberían estar en api.php si la lógica es manejada por Alpine.js/Fetch
    // pero las mantenemos aquí ya que no especificaste moverlas.
    Route::post('/carrito/agregar/{id}', [PanelController::class,'agregarAlCarrito'])->name('carrito.agregar');
    Route::post('/carrito/remover/{id}', [PanelController::class,'removerDelCarrito'])->name('carrito.remover');
    Route::post('/carrito/actualizar/{id}', [PanelController::class,'actualizarCarrito'])->name('carrito.actualizar');
    Route::post('/carrito/limpiar', [PanelController::class,'limpiarCarrito'])->name('carrito.limpiar');
    Route::post('/carrito/finalizar', [PanelController::class,'finalizarVenta'])->name('carrito.finalizar');
    Route::post('/carrito/apartado', [PanelController::class,'crearApartado'])->name('carrito.apartado');

    // Módulos de Clientes
    Route::post('/clientes', [ClienteController::class, 'guardar'])->name('clientes.guardar'); 

    // Módulos principales
    Route::get('/estadisticas', [EstadisticasController::class,'index'])->name('estadisticas.index'); 

    // Inventario
    Route::get('/inventario', [InventarioController::class,'index'])->name('inventario.index'); 
    Route::post('/inventario', [InventarioController::class,'guardar'])->name('inventario.guardar');

    // Proveedores
    Route::get('/proveedores', [ProveedoresController::class,'index'])->name('proveedores.index'); 
    Route::post('/proveedores', [ProveedoresController::class,'guardar'])->name('proveedores.guardar');

    // Apartados
    Route::get('/apartados', [ApartadoController::class,'index'])->name('apartados.index');
    Route::post('/apartados/crear', [ApartadoController::class,'guardar'])->name('apartados.guardar'); 

    // Ventas
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
});
