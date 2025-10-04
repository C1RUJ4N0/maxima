<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Apartado;
use App\Models\Egreso; // Se importa el nuevo modelo
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    // MÉTODO PARA LA API
    public function apiIndex()
    {
        $ventasHoy = Venta::whereDate('created_at', Carbon::today())->sum('monto_total');
        $ventasMes = Venta::whereMonth('created_at', Carbon::now()->month)->sum('monto_total');
        $egresos = Egreso::whereMonth('created_at', Carbon::now()->month)->sum('monto'); // ¡Ahora es dinámico!

        $productosBajoStock = Producto::where('existencias', '<=', 10)->get(['id', 'nombre', 'existencias']);
        
        $apartadosVigentes = Apartado::where('estado', 'vigente')
            ->with('cliente:id,nombre') // Se ajusta a 'nombre' del modelo Cliente
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
            'egresos' => (float) $egresos, // El dato ahora es real
            'productosBajoStock' => $productosBajoStock,
            'apartadosVigentes' => $apartadosVigentes,
        ]);
    }
}