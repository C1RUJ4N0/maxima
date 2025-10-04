<?php

namespace App\Http\Controllers; // <-- Con barra invertida \

use App\Models\Proveedor;
use App\Models\Factura;
use Illuminate\Http\Request; // <-- Con barra invertida \
use Illuminate\Support\Facades\Validator; // <-- Con barra invertida \

class ProveedoresController extends Controller
{
    /**
     * Devuelve la lista de todos los proveedores.
     */
    public function apiIndex()
    {
        return response()->json(['proveedores' => Proveedor::orderBy('nombre')->get()]);
    }

    /**
     * Devuelve los detalles de un proveedor específico, incluyendo sus facturas.
     */
    public function apiShow($id)
    {
        $proveedor = Proveedor::with('facturas')->findOrFail($id);
        return response()->json($proveedor);
    }
    
    /**
     * Guarda un nuevo proveedor en la base de datos.
     */
    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255|unique:proveedores,email',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $proveedor = Proveedor::create($request->all());
        return response()->json($proveedor, 201);
    }

    /**
     * Guarda una nueva factura para un proveedor específico.
     */
    public function apiStoreFactura(Request $request, $id_proveedor)
    {
        $validator = Validator::make($request->all(), [
            'numero_factura' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $factura = new Factura($request->all());
        $factura->proveedor_id = $id_proveedor;
        $factura->save();
        
        return response()->json($factura, 201);
    }
}