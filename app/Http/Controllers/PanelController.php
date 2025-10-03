<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\ItemVenta;
use App\Models\Apartado;
use App\Models\ItemApartado;
use Illuminate\Support\Facades\DB;

class PanelController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        $clientes = Cliente::all();

        return view('/panel.index', compact('productos', 'clientes'));
    }

    public function agregarAlCarrito(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $carrito = session()->get('carrito', []);

        if(isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
        } else {
            $carrito[$id] = [
                "nombre" => $producto->nombre,
                "precio" => $producto->precio,
                "cantidad" => 1
            ];
        }

        session()->put('carrito', $carrito);
        return response()->json(['carrito' => $carrito]);
    }

    public function eliminarDelCarrito(Request $request, $id)
    {
        $carrito = session()->get('carrito', []);
        if(isset($carrito[$id])) {
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
        }
        return response()->json(['carrito' => $carrito]);
    }

    public function actualizarCarrito(Request $request, $id)
    {
        $carrito = session()->get('carrito', []);
        $cantidad = (int) $request->input('cantidad', 0);
        if(isset($carrito[$id]) && $cantidad > 0) {
            $carrito[$id]['cantidad'] = $cantidad;
            session()->put('carrito', $carrito);
        }
        return response()->json(['carrito' => $carrito]);
    }

    public function limpiarCarrito()
    {
        session()->forget('carrito');
        return response()->json(['mensaje' => 'Carrito limpiado']);
    }

    public function crearVenta(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if(empty($carrito)) {
            return back()->with('error', 'El carrito está vacío.');
        }

        DB::transaction(function() use ($request, $carrito) {
            $total = collect($carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']);

            $venta = Venta::create([
                'nombre_cliente' => $request->nombre_cliente,
                'monto_total' => $total,
            ]);

            foreach($carrito as $productoId => $item) {
                ItemVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $productoId,
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                ]);

                $producto = Producto::find($productoId);
                $producto->decrement('stock', $item['cantidad']);
            }

            session()->forget('carrito');
        });

        return back()->with('success', 'Venta realizada correctamente.');
    }

    public function crearApartado(Request $request)
    {
        $carrito = session()->get('carrito', []);
        if(empty($carrito)) {
            return back()->with('error', 'El carrito está vacío.');
        }

        DB::transaction(function() use ($request, $carrito) {
            $total = collect($carrito)->sum(fn($item) => $item['precio'] * $item['cantidad']);

            $apartado = Apartado::create([
                'nombre_cliente' => $request->nombre_cliente,
                'monto_total' => $total,
                'monto_pagado' => $request->monto_pagado ?? 0,
                'estado' => 'vigente',
            ]);

            foreach($carrito as $productoId => $item) {
                ItemApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $productoId,
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                ]);
            }

            session()->forget('carrito');
        });

        return back()->with('success', 'Apartado creado correctamente.');
    }
}
