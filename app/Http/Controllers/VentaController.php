<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\ItemVenta;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'carrito' => 'required|array|min:1',
            'carrito.*.id' => 'required|integer|exists:productos,id',
            'carrito.*.cantidad' => 'required|integer|min:1',
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'monto_recibido' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia',
        ]);

        DB::beginTransaction();
        try {
            $totalVenta = 0;
            foreach ($validatedData['carrito'] as $item) {
                $producto = Producto::findOrFail($item['id']);
                if ($producto->existencias < $item['cantidad']) {
                    throw new \Exception('Stock insuficiente para: ' . $producto->nombre);
                }
                $totalVenta += $item['cantidad'] * $producto->precio;
            }

            $venta = Venta::create([
                'cliente_id' => $validatedData['cliente_id'],
                'monto_total' => $totalVenta,
                'monto_recibido' => $validatedData['monto_recibido'],
                'cambio' => $validatedData['monto_recibido'] - $totalVenta,
                'users_id' => Auth::id(),
                'metodo_pago' => $validatedData['metodo_pago'],
            ]);

            foreach ($validatedData['carrito'] as $item) {
                $producto = Producto::findOrFail($item['id']);
                ItemVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $producto->precio,
                ]);
                $producto->decrement('existencias', $item['cantidad']);
            }

            DB::commit();
            return response()->json(['message' => 'Venta registrada. Stock actualizado.'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al registrar la venta: " . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}