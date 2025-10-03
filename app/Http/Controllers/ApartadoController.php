<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\Cliente; 
use App\Models\ItemApartado;
use App\Models\Producto; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApartadoController extends Controller
{
    public function index()
    {
        $apartados = Apartado::with('cliente')->orderBy('fecha_vencimiento')->get();
        $clientes = Cliente::all();
        $articulosApartados = ItemApartado::all();
        $productos = Producto::all();

        return view('apartados.index', compact('apartados', 'clientes', 'articulosApartados', 'productos'));
    }

    public function store(Request $request)
    {
        $datosValidados = $request->validate([
            'cliente_id' => 'required|exists:clientes,id', 
            'monto_total' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date',
            'articulos' => 'required|array',
            'articulos.*.producto_id' => 'required|exists:productos,id', 
            'articulos.*.cantidad' => 'required|integer|min:1',
            'articulos.*.precio' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $apartado = Apartado::create([
                'cliente_id' => $datosValidados['cliente_id'],
                'monto_total' => $datosValidados['monto_total'],
                'monto_pagado' => $datosValidados['monto_pagado'],
                'fecha_vencimiento' => $datosValidados['fecha_vencimiento'],
                'estado' => 'vigente',
            ]);

            foreach ($datosValidados['articulos'] as $articulo) {
                ItemApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $articulo['producto_id'], 
                    'cantidad' => $articulo['cantidad'],
                    'precio' => $articulo['precio'],
                ]);

                $producto = Producto::find($articulo['producto_id']);
                $producto->existencias -= $articulo['cantidad'];
                $producto->save();
            }

            DB::commit();

            return redirect()->route('apartados.index')->with('success', 'Apartado creado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hubo un error al crear el apartado: ' . $e->getMessage());
        }
    }
}
