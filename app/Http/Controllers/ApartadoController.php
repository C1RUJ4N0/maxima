<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use Illuminate\Http\Request;

class ApartadoController extends Controller
{
    public function index()
    {
        return view('apartados.index');
    }

    // MÉTODO NUEVO: Para la API de tu panel
    public function apiIndex()
    {
        $apartados = Apartado::with('cliente:id,name,telefono')->get()->map(fn($a) => [
            'id' => $a->id,
            'nombre_cliente' => $a->cliente->name ?? 'N/A',
            'telefono' => $a->cliente->telefono ?? 'N/A',
            'monto' => $a->monto_total,
            'fecha_vencimiento' => $a->fecha_vencimiento,
            'estado' => $a->estado,
        ]);
        return response()->json(['apartados' => $apartados]);
    }

    public function guardar(Request $request)
    {
        // Lógica para guardar apartados
    }
}