<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Egreso;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PanelController extends Controller
{
    /**
     * Muestra el panel principal con las estadísticas clave.
     * ESTA ES LA LÓGICA QUE FALTABA.
     */
    public function index()
    {
        // Calcula las ventas realizadas hoy
        $ventasHoy = Venta::whereDate('created_at', Carbon::today())->sum('monto_total');

        // Calcula las ventas realizadas en el mes actual
        $ventasMes = Venta::whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->sum('monto_total');

        // Suma todos los egresos registrados
        $egresos = Egreso::sum('monto');

        // Pasa las variables a la vista del panel
        return view('panel.index', [
            'ventasHoy' => $ventasHoy,
            'ventasMes' => $ventasMes,
            'egresos' => $egresos,
        ]);
    }
}

