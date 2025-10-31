<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Apartado;
use App\Models\Factura;
use App\Models\Egreso;
use App\Models\Producto;
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    // Método para la vista Blade separada (si existe)
    public function index()
    {
        // ... Lógica para la vista Blade ...
        return view('estadisticas.index');
    }

    /**
     * Devuelve las estadísticas para el panel TPV.
     */
    public function apiIndex()
    {
        // --- LÓGICA DE VENTAS (INGRESOS) ACTUALIZADA ---
        $ventasHoy = Venta::whereDate('created_at', Carbon::today())->sum('monto_total');
        // Suma Apartados que fueron marcados como 'pagado' HOY
        $apartadosPagadosHoy = Apartado::where('estado', 'pagado')
                                      ->whereDate('updated_at', Carbon::today()) // Asume que updated_at refleja cuándo se pagó
                                      ->sum('monto_total');
        
        $ventasMes = Venta::whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year)
                          ->sum('monto_total');
        // Suma Apartados que fueron marcados como 'pagado' ESTE MES
        $apartadosPagadosMes = Apartado::where('estado', 'pagado')
                                       ->whereMonth('updated_at', Carbon::now()->month)
                                       ->whereYear('updated_at', Carbon::now()->year)
                                       ->sum('monto_total');

        // --- LÓGICA DE EGRESOS ACTUALIZADA ---
        // Egresos son Facturas pagadas + Egresos manuales
        $egresosFacturas = Factura::where('estado', 'pagada')->sum('monto');
        $egresosManuales = Egreso::sum('monto'); // (Tu modelo Egreso)

        // --- OTRAS ESTADÍSTICAS ---
        $productosBajoStock = Producto::where('existencias', '<', 10)
                                    ->orderBy('existencias')
                                    ->get(['id', 'nombre', 'existencias']);
        
        $apartadosVigentes = Apartado::with('cliente')
                                     ->where('estado', 'vigente')
                                     ->get()
                                     ->map(function ($a) {
                                         return [
                                             'id' => $a->id,
                                             'cliente_nombre' => $a->cliente->nombre,
                                             'monto_total' => $a->monto_total,
                                             'fecha_vencimiento' => $a->fecha_vencimiento,
                                         ];
                                     });

        return response()->json([
            'ventasHoy' => $ventasHoy + $apartadosPagadosHoy,
            'ventasMes' => $ventasMes + $apartadosPagadosMes,
            'egresos' => $egresosFacturas + $egresosManuales,
            'productosBajoStock' => $productosBajoStock,
            'apartadosVigentes' => $apartadosVigentes,
        ]);
    }
}