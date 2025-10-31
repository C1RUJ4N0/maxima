<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\ItemVenta;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VentaController extends Controller
{
    /**
     * Almacena una nueva venta desde el TPV.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'monto_recibido' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia',
            'carrito' => 'required|array|min:1',
            'carrito.*.id' => 'required|exists:productos,id',
            'carrito.*.cantidad' => 'required|integer|min:1',
            'carrito.*.precio_venta' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $montoTotal = 0;
            foreach ($validatedData['carrito'] as $item) {
                $montoTotal += $item['precio_venta'] * $item['cantidad'];
            }

            if ($validatedData['monto_recibido'] < $montoTotal) {
                throw new \Exception('El monto recibido es menor al total.');
            }

            $venta = Venta::create([
                'cliente_id' => $validatedData['cliente_id'],
                'monto_total' => $montoTotal,
                'monto_recibido' => $validatedData['monto_recibido'],
                'monto_cambio' => $validatedData['monto_recibido'] - $montoTotal,
                'metodo_pago' => $validatedData['metodo_pago'],
                'users_id' => Auth::id(),
            ]);

            foreach ($validatedData['carrito'] as $item) {
                $producto = Producto::findOrFail($item['id']);
                if ($producto->existencias < $item['cantidad']) {
                    throw new \Exception('Stock insuficiente para: ' . $producto->nombre);
                }

                ItemVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    // --- ¡¡¡AQUÍ ESTÁ LA CORRECCIÓN!!! ---
                    'precio' => $item['precio_venta'], // El campo se llama 'precio', no 'precio_venta'
                    // --- FIN DE LA CORRECCIÓN ---
                ]);

                // Decrementa el stock
                $producto->decrement('existencias', $item['cantidad']);
            }

            DB::commit();
            return response()->json(['message' => 'Venta finalizada con éxito.', 'venta' => $venta], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al finalizar la venta: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo procesar la venta: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Muestra la lista de ventas para el panel TPV.
     */
    public function apiIndex()
    {
        $ventas = Venta::with('cliente')
                        ->orderByDesc('created_at')
                        ->take(50)
                        ->get();
                        
        return response()->json($ventas);
    }

    /**
     * Actualiza el método de pago de una venta.
     */
    public function apiUpdate(Request $request, Venta $venta)
    {
        $validatedData = $request->validate([
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia',
        ]);

        try {
            $venta->update([
                'metodo_pago' => $validatedData['metodo_pago']
            ]);
            return response()->json(['message' => 'Venta actualizada.', 'venta' => $venta]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar venta: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo actualizar la venta.'], 500);
        }
    }

    /**
     * Elimina una venta y devuelve el stock al inventario.
     */
    public function apiDestroy(Venta $venta)
    {
        DB::beginTransaction();
        try {
            $items = ItemVenta::where('venta_id', $venta->id)->get();

            foreach ($items as $item) {
                $producto = Producto::find($item->producto_id);
                if ($producto) {
                    $producto->increment('existencias', $item->cantidad);
                }
                $item->delete();
            }

            $venta->delete();

            DB::commit();
            return response()->json(['message' => 'Venta eliminada y stock devuelto.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar la venta: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo eliminar la venta.'], 500);
        }
    }
}