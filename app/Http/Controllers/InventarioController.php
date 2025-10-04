<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventarioController extends Controller
{
    // Muestra la vista principal del inventario (no cambia)
    public function index()
    {
        return view('inventario.index');
    }

    // MÉTODO MODIFICADO: Busca productos solo si se envía un parámetro 'q'
    public function apiIndex(Request $request)
    {
        $terminoBusqueda = $request->input('q');

        if ($terminoBusqueda && strlen($terminoBusqueda) > 0) {
            $productos = Producto::where('nombre', 'like', "%{$terminoBusqueda}%")->get();
        } else {
            $productos = collect(); // Devuelve una colección vacía si no hay búsqueda
        }
        
        return response()->json(['productos' => $productos]);
    }

    // MÉTODO PARA GUARDAR PRODUCTOS: Se mantiene para la funcionalidad de añadir producto
    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:productos',
            'precio' => 'required|numeric|min:0',
            'existencias' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto = Producto::create($request->all());
        return response()->json($producto, 201);
    }
}