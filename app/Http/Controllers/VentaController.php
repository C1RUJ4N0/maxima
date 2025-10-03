<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\ItemVenta;
use App\Models\ItemApartado;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function obtenerDatosPanel()
    {
        try {
            $productos = Producto::all();
            $clientes = Cliente::all();

            return response()->json([
                'productos' => $productos,
                'clientes' => $clientes
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos del panel: ' . $e->getMessage());
            return response()->json(['mensaje' => 'Error al obtener los datos.', 'error' => $e->getMessage()], 500);
        }
    }

    public function almacenarProducto(Request $request)
    {
        try {
            DB::beginTransaction();
            // VALIDACIÓN ORIGINAL (se mantiene 'existencias' como campo del modelo)
            $request->validate([
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'existencias' => 'required|integer|min:0',
                'descripcion' => 'nullable|string',
            ]);

            // AJUSTE: Si el frontend envía 'stock', lo mapeamos a 'existencias' (campo del modelo).
            $data = $request->all();
            if (isset($data['stock'])) {
                $data['existencias'] = $data['stock'];
                unset($data['stock']);
            }
            $producto = Producto::create($data);

            DB::commit();
            return response()->json(['mensaje' => 'Producto creado con éxito.', 'producto' => $producto], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear producto: ' . $e->getMessage());
            return response()->json(['mensaje' => 'Error al crear el producto.', 'error' => $e->getMessage()], 500);
        }
    }

    public function actualizarProducto(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'existencias' => 'required|integer|min:0',
                'descripcion' => 'nullable|string',
            ]);

            $producto = Producto::findOrFail($id);
            $producto->update($request->all());

            DB::commit();
            return response()->json(['mensaje' => 'Producto actualizado con éxito.', 'producto' => $producto], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar producto: ' . $e->getMessage());
            return response()->json(['mensaje' => 'Error al actualizar el producto.', 'error' => $e->getMessage()], 500);
        }
    }

    public function eliminarProducto($id)
    {
        try {
            DB::beginTransaction();
            $producto = Producto::findOrFail($id);
            $producto->delete();
            DB::commit();
            return response()->json(['mensaje' => 'Producto eliminado con éxito.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar producto: ' . $e->getMessage());
            return response()->json(['mensaje' => 'Error al eliminar el producto.', 'error' => $e->getMessage()], 500);
        }
    }

    public function almacenarVenta(Request $request)
    {
        try {
            DB::beginTransaction();
            // AJUSTE DE VALIDACIÓN: Aceptamos los campos que realmente envía el frontend
            $request->validate([
                'clienteIdSeleccionado' => 'required|exists:clientes,id',
                'montoTotal' => 'required|numeric|min:0',
                'items' => 'required|array',
                'items.*.id' => 'required|exists:productos,id',
                'items.*.cantidad' => 'required|integer|min:1',
            ]);

            $venta = Venta::create([
                'cliente_id' => $request->clienteIdSeleccionado,
                'monto_total' => $request->montoTotal,
            ]);

            foreach ($request->items as $item) {
                
                $producto = Producto::find($item['id']);

                // NUEVA LÓGICA: Verificar stock (existencias) antes de la venta
                if ($producto->existencias < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json(['mensaje' => 'Stock insuficiente para el producto ' . $producto->nombre . '.', 'error' => ['stock' => ['Stock insuficiente.']]], 409);
                }

                ItemVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    // Usar el precio del producto en la DB
                    'precio' => $producto->precio,
                ]);

                $producto->existencias -= $item['cantidad'];
                $producto->save();
            }

            DB::commit();
            // CAMBIO: Devolver 'id_venta' para el recibo en el frontend
            return response()->json(['mensaje' => 'Venta creada con éxito.', 'venta' => $venta, 'id_venta' => $venta->id], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear venta: ' . $e->getMessage());
            return response()->json(['mensaje' => 'Error al crear la venta.', 'error' => $e->getMessage()], 500);
        }
    }

    public function almacenarApartado(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'items' => 'required|array',
                'items.*.producto_id' => 'required|exists:productos,id',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.precio' => 'required|numeric|min:0',
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            $apartado = Apartado::create([
                'cliente_id' => $request->cliente_id,
                'monto_total' => $total,
                'fecha_vencimiento' => Carbon::now()->addDays(7),
                'estado' => 'vigente',
            ]);

            foreach ($request->items as $item) {
                ItemApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                ]);
            }

            DB::commit();
            return response()->json(['mensaje' => 'Apartado creado con éxito.', 'apartado' => $apartado], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear apartado: ' . $e->getMessage());
            return response()->json(['mensaje' => 'Error al crear el apartado.', 'error' => $e->getMessage()], 500);
        }
    }

    public function obtenerApartados()
    {
        try {
            $apartados = Apartado::with('cliente')->get()->map(function($apartado) {
                return [
                    'nombre_cliente' => $apartado->cliente->nombre,
                    'telefono' => $apartado->cliente->numero_telefono,
                    'monto' => $apartado->monto_total,
                    'fecha_vencimiento' => $apartado->fecha_vencimiento,
                    'estado' => $apartado->estado
                ];
            });
            return response()->json(['apartados' => $apartados]);
        } catch (\Exception $e) {
            Log::error('Error al obtener apartados: ' . $e->getMessage());
            return response()->json(['mensaje' => 'Error al obtener los apartados.'], 500);
        }
    }
}