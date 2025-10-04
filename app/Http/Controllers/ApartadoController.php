<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\ItemApartado;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApartadoController extends Controller
{
    public function index()
    {
        return view('apartados.index');
    }

    public function apiIndex()
    {
        $apartados = Apartado::with('cliente:id,nombre,telefono')
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'nombre_cliente' => $a->cliente->nombre ?? 'N/A',
                'telefono' => $a->cliente->telefono ?? 'N/A',
                'monto' => $a->monto_total,
                'fecha_vencimiento' => $a->fecha_vencimiento,
                'estado' => $a->estado,
            ]);
            
        return response()->json($apartados);
    }

    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|integer|exists:clientes,id',
            'monto_total' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0', // CORREGIDO a español
            'fecha_vencimiento' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $apartado = Apartado::create([
                'cliente_id' => $request->cliente_id,
                'monto_total' => $request->monto_total,
                'monto_pagado' => $request->monto_pagado, // CORREGIDO a español
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'estado' => 'vigente',
            ]);

            foreach ($request->items as $itemData) {
                $producto = Producto::find($itemData['id']);

                if ($producto->existencias < $itemData['cantidad']) {
                    DB::rollBack();
                    return response()->json(['message' => "Stock insuficiente para: {$producto->nombre}"], 409);
                }

                ItemApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $itemData['cantidad'],
                    'precio_unitario' => $producto->precio,
                ]);

                $producto->decrement('existencias', $itemData['cantidad']);
            }

            DB::commit();
            return response()->json($apartado, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear el apartado: ' . $e->getMessage()], 500);
        }
    }
}