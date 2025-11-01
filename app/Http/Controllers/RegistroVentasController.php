<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta; // <-- AÑADIDO

class RegistroVentasController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(Request $request)
    {
        // --- ESTA ES LA CORRECCIÓN ---
        // Buscamos las ventas y las pasamos a la vista
        $ventas = Venta::with('cliente')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20); // O usa ::all() si prefieres

        return view('registro_ventas.index', compact('ventas'));
        // --- FIN DE LA CORRECCIÓN ---
    }
}