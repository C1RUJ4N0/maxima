<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartado;
use App\Models\ItemApartado;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApartadoController extends Controller
{
    public function apiIndex()
    {
        $apartados = Apartado::with('cliente')
            ->where('estado', 'vigente')
            ->get()
            ->map(function ($apartado) {
                return [
                    'id' => $apartado->id,
                    'nombre_cliente' => $apartado->cliente->nombre,
                    'telefono' => $apartado->cliente->telefono,
                    'monto' => $apartado->monto_restante,
                    'fecha_vencimiento' => $apartado->fecha_vencimiento,
                    'estado' => $apartado->estado,
                ];
            });
        return response()->json($apartados);
    }

    public function apiStore(Request $request)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'monto_total' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date|after_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $montoRestante = $validatedData['monto_total'] - $validatedData['monto_pagado'];
            if ($montoRestante < 0) {
                throw new \Exception('El monto pagado no puede ser mayor al monto total.');
            }

            $apartado = Apartado::create([
                'cliente_id' => $validatedData['cliente_id'],
                'monto_total' => $validatedData['monto_total'],
                'monto_pagado' => $validatedData['monto_pagado'],
                'monto_restante' => $montoRestante,
                'fecha_vencimiento' => $validatedData['fecha_vencimiento'],
                'estado' => 'vigente',
                'users_id' => Auth::id(),
            ]);

            foreach ($validatedData['items'] as $item) {
                $producto = Producto::findOrFail($item['id']);
                if ($producto->existencias < $item['cantidad']) {
                    throw new \Exception('Stock insuficiente para apartar: ' . $producto->nombre);
                }
                ItemApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $producto->precio,
                ]);
                $producto->decrement('existencias', $item['cantidad']);
            }

            DB::commit();
            return response()->json($apartado, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear apartado: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo crear el apartado: ' . $e->getMessage()], 500);
        }
    }
}