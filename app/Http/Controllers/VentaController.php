<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\ItemVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VentaController extends Controller
{
    public function finalizarVentaApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'clienteIdSeleccionado' => 'required|integer|exists:clientes,id',
            'montoTotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $venta = Venta::create([
                'cliente_id' => $request->clienteIdSeleccionado,
                'total' => $request->montoTotal,
                'fecha_venta' => now(),
            ]);

            foreach ($request->items as $item) {
                $producto = Producto::find($item['id']);

                if ($producto->existencias < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json(['error' => ['stock' => ["Stock insuficiente para: {$producto->nombre}"]]], 409);
                }

                ItemVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                ]);

                $producto->decrement('existencias', $item['cantidad']);
            }

            DB::commit();
            return response()->json(['mensaje' => 'Venta finalizada', 'id_venta' => $venta->id], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error en la transacción: ' . $e->getMessage()], 500);
        }
    }
}