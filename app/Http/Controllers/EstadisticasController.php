<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Apartado;
use Carbon\Carbon;

class EstadisticasController extends Controller
{
    // MÉTODO PARA LA API
    public function apiIndex()
    {
        $ventasHoy = Venta::whereDate('created_at', Carbon::today())->sum('total');
        $ventasMes = Venta::whereMonth('created_at', Carbon::now()->month)->sum('total');
        $productosBajoStock = Producto::where('existencias', '<=', 10)->get(['id', 'nombre', 'existencias']);
        $apartadosVigentes = Apartado::where('estado', 'vigente')
            ->with('cliente:id,name')
            ->get(['id', 'cliente_id', 'monto_total', 'fecha_vencimiento'])
            ->map(fn($a) => [
                'id' => $a->id,
                'cliente_nombre' => $a->cliente->name ?? 'N/A',
                'monto_total' => $a->monto_total,
                'fecha_vencimiento' => $a->fecha_vencimiento,
            ]);

        return response()->json([
            'ventasHoy' => (float) $ventasHoy,
            'ventasMes' => (float) $ventasMes,
            'egresos' => 1350.75, // Dato DUMMY como en tu frontend
            'productosBajoStock' => $productosBajoStock,
            'apartadosVigentes' => $apartadosVigentes,
        ]);
    }
}