<?php
namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Egreso;
use App\Models\Producto;
use App\Models\Apartado;
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    // Este método es para la otra vista de estadísticas con gráficos. NO es para el panel TPV.
    public function index()
    {
        // ... (Aquí va la lógica de la otra vista de estadísticas, si la tienes)
    }

    // Este es el método que usa tu PANEL TPV.
    public function apiIndex()
    {
        $ventasHoy = Venta::whereDate('created_at', Carbon::today())->sum('monto_total');
        $ventasMes = Venta::whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year)
                          ->sum('monto_total');
        
        $egresos = Egreso::sum('monto');

        $productosBajoStock = Producto::where('existencias', '<=', 10)->orderBy('existencias')->get(['id', 'nombre', 'existencias']);
        
        $apartadosVigentes = Apartado::where('estado', 'vigente')
            ->with('cliente:id,nombre')->orderBy('fecha_vencimiento')
            ->get(['id', 'cliente_id', 'monto_total', 'fecha_vencimiento'])
            ->map(fn($a) => [
                'id' => $a->id,
                'cliente_nombre' => $a->cliente->nombre ?? 'N/A',
                'monto_total' => $a->monto_total,
                'fecha_vencimiento' => $a->fecha_vencimiento,
            ]);

        return response()->json([
            'ventasHoy' => (float) $ventasHoy,
            'ventasMes' => (float) $ventasMes,
            'egresos' => (float) $egresos, // Egresos totales
            'productosBajoStock' => $productosBajoStock,
            'apartadosVigentes' => $apartadosVigentes,
        ]);
    }
}