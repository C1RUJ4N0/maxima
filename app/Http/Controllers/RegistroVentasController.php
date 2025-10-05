<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistroVentasController extends Controller
{
    public function index()
    {
        $ventasPorDia = Venta::select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw("SUM(CASE WHEN metodo_pago = 'efectivo' THEN monto_total ELSE 0 END) as efectivo"),
                DB::raw("SUM(CASE WHEN metodo_pago = 'transferencia' THEN monto_total ELSE 0 END) as transferencia"),
                DB::raw("SUM(CASE WHEN metodo_pago = 'tarjeta' THEN monto_total ELSE 0 END) as tarjeta"),
                DB::raw("SUM(CASE WHEN metodo_pago = 'apartado' THEN monto_total ELSE 0 END) as apartado"),
                DB::raw('SUM(monto_total) as total_dia')
            )
            ->groupBy('fecha')
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        return view('registro_ventas.index', compact('ventasPorDia'));
    }
}