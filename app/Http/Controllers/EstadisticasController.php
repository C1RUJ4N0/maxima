<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Apartado;

class EstadisticasController extends Controller
{
    public function index()
    {
        $ventasHoy = Venta::whereDate('created_at', today())->sum('monto_total');

        $ventasMes = Venta::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->sum('monto_total');

        $egresos = 500.00; // Valor de ejemplo

        $productosBajoStock = Producto::where('existencias', '<=', 10)->get();

        $apartadosVigentes = Apartado::where('estado', 'vigente')
                                    ->with('cliente') // Cargamos la relación del cliente
                                    ->get()
                                    ->map(function($apartado) {
                                        return [
                                            'id' => $apartado->id,
                                            'monto_total' => $apartado->monto_total,
                                            'fecha_vencimiento' => $apartado->fecha_vencimiento,
                                            'cliente_nombre' => $apartado->cliente->nombre ?? 'Sin Cliente'
                                        ];
                                    });

        // CAMBIO: Devolver JSON con todas las estadísticas
        return response()->json([
            'ventasHoy' => $ventasHoy,
            'ventasMes' => $ventasMes,
            'egresos' => $egresos,
            'productosBajoStock' => $productosBajoStock,
            'apartadosVigentes' => $apartadosVigentes,
        ]);
    }
}