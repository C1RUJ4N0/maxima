<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controlador de Vistas para la navegación en el sistema POS.
 */
class VistaController extends Controller
{
    /**
     * Muestra la vista principal del Dashboard (Punto de Venta).
     * Corresponde al archivo 'dashboard.blade.php'.
     */
    public function mostrarDashboard()
    {
        return view('dashboard');
    }
    
    /**
     * Muestra la vista de Estadísticas.
     */
    public function mostrarEstadisticas()
    {
        return view('estadisticas');
    }

    /**
     * Muestra la vista de Inventario.
     */
    public function mostrarInventario()
    {
        return view('inventario');
    }

    /**
     * Muestra la vista de Proveedores.
     */
    public function mostrarProveedores()
    {
        return view('proveedores');
    }

    /**
     * Muestra la vista de Apartados.
     */
    public function mostrarApartados()
    {
        return view('apartados');
    }
}
