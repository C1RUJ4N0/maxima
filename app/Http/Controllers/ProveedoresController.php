<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Factura;
use App\Models\Egreso; // <-- IMPORTANTE: Añadir el modelo Egreso
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProveedoresController extends Controller
{
    /**
     * Devuelve todos los proveedores para la API.
     */
    public function apiIndex()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return response()->json(['proveedores' => $proveedores]);
    }

    /**
     * Almacena un nuevo proveedor desde la API.
     */
    public function apiStore(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:proveedores',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $proveedor = Proveedor::create($validatedData);

        return response()->json($proveedor, 201);
    }

    /**
     * Muestra los detalles de un proveedor específico para la API.
     */
    public function apiShow(Proveedor $proveedor)
    {
        // Cargar las facturas asociadas al proveedor
        $proveedor->load('facturas');
        return response()->json($proveedor);
    }

    /**
     * Almacena una nueva factura para un proveedor y crea un egreso automáticamente.
     * ESTA ES LA FUNCIÓN CORREGIDA.
     */
    public function apiStoreFactura(Request $request, Proveedor $proveedor)
    {
        $validatedData = $request->validate([
            'numero_factura' => 'required|string|unique:facturas',
            'monto' => 'required|numeric|min:0.01',
            'fecha_emision' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            // 1. Crear la factura y marcarla como pagada
            $factura = $proveedor->facturas()->create([
                'numero_factura' => $validatedData['numero_factura'],
                'monto' => $validatedData['monto'],
                'fecha_emision' => $validatedData['fecha_emision'],
                'estado' => 'pagada', // Se guarda como 'pagada' porque el egreso es inmediato
            ]);

            // 2. Crear el registro del Egreso (gasto)
            Egreso::create([
                'descripcion' => 'Factura N°' . $factura->numero_factura . ' de ' . $proveedor->nombre,
                'monto' => $factura->monto,
            ]);

            DB::commit(); // Confirmar ambos cambios en la base de datos

            return response()->json($factura, 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Si algo falla, revertir todo
            Log::error("Error al guardar factura y crear egreso: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo registrar la factura y el egreso.'], 500);
        }
    }
}