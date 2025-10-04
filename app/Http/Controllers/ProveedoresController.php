<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProveedoresController extends Controller
{
    // Método para la API (ya debería existir)
    public function apiIndex()
    {
        return response()->json(['proveedores' => Proveedor::orderBy('nombre')->get()]);
    }

    // MODIFICACIÓN: Método para guardar un nuevo proveedor desde la API
    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'persona_contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:proveedores',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $proveedor = Proveedor::create($request->all());

        return response()->json(['proveedor' => $proveedor], 201);
    }
}