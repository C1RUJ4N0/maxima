<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InventarioController extends Controller
{
    public function index()
    {
        return view('inventario.index');
    }

    // MÉTODO NUEVO: Para obtener productos en la API
    public function apiIndex()
    {
        return response()->json(['productos' => Producto::all()]);
    }

    // MÉTODO NUEVO: Para guardar un producto desde la API
    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:productos',
            'precio' => 'required|numeric|min:0',
            'existencias' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['mensaje' => 'Datos inválidos', 'errors' => $validator->errors()], 422);
        }

        $producto = Producto::create($request->all());
        return response()->json(['producto' => $producto], 201);
    }
}