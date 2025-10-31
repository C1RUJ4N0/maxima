<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartado;
use App\Models\ItemApartado;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;        // <-- AÑADIR Venta
use App\Models\ItemVenta;  // <-- AÑADIR ItemVenta
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApartadoController extends Controller
{
    // (Métodos Web: index, store, update, destroy ... )
    // (Mantengo estos métodos por si los usas en otras partes del sistema)
    public function index() { 
        $apartados = Apartado::with('cliente')->orderByDesc('created_at')->get();
        $clientes = Cliente::orderBy('nombre')->get(); 
        return view('apartados.index', compact('apartados', 'clientes'));
    }
    public function store(Request $request) { /* Lógica del store web */ }
    public function update(Request $request, Apartado $apartado) { /* Lógica del update web */ }
    public function destroy(Apartado $apartado) { /* Lógica del destroy web */ }


    // --- MÉTODOS API PARA EL TPV ---

    public function apiIndex()
    {
        $apartados = Apartado::with('cliente')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($apartado) {
                return [
                    'id' => $apartado->id,
                    'nombre_cliente' => $apartado->cliente ? $apartado->cliente->nombre : 'Cliente Eliminado',
                    'telefono' => $apartado->cliente ? $apartado->cliente->telefono : 'N/A',
                    'monto' => $apartado->monto_restante,
                    'monto_total' => $apartado->monto_total,
                    'monto_pagado' => $apartado->monto_pagado,
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

            $estado = ($montoRestante <= 0) ? 'pagado' : 'vigente';

            $apartado = Apartado::create([
                'cliente_id' => $validatedData['cliente_id'],
                'monto_total' => $validatedData['monto_total'],
                'monto_pagado' => $validatedData['monto_pagado'],
                'monto_restante' => $montoRestante,
                'fecha_vencimiento' => $validatedData['fecha_vencimiento'],
                'estado' => $estado,
                'users_id' => Auth::id(),
                'fecha_apartado' => now()
            ]);

            $itemsParaVenta = [];

            foreach ($validatedData['items'] as $item) {
                $producto = Producto::findOrFail($item['id']);
                if ($producto->existencias < $item['cantidad']) {
                    throw new \Exception('Stock insuficiente para apartar: ' . $producto->nombre);
                }
                
                $itemApartado = ItemApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $producto->precio,
                ]);
                $itemsParaVenta[] = $itemApartado;

                // REGLA: Quitar stock SIEMPRE (sea 'vigente' o 'pagado' al nacer)
                $producto->decrement('existencias', $item['cantidad']);
            }

            // REGLA: Si nace 'pagado', crear la Venta inmediatamente
            if ($estado == 'pagado') {
                $venta = Venta::create([
                    'cliente_id' => $apartado->cliente_id,
                    'monto_total' => $apartado->monto_total,
                    'monto_recibido' => $apartado->monto_pagado,
                    'monto_cambio' => 0,
                    'metodo_pago' => 'apartado-pagado',
                    'users_id' => $apartado->users_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($itemsParaVenta as $item) {
                    ItemVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $item->producto_id,
                        'cantidad' => $item->cantidad,
                        'precio' => $item->precio,
                    ]);
                }
            }

            DB::commit();
            return response()->json($apartado, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al crear apartado: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo crear el apartado: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Actualiza un apartado existente (API para TPV).
     */
    public function apiUpdate(Request $request, Apartado $apartado)
    {
        $validatedData = $request->validate([
            'monto_pagado' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date',
            'estado' => 'required|string|in:vigente,pagado,cancelado',
        ]);

        DB::beginTransaction();
        try {
            $oldState = $apartado->estado;
            $newState = $validatedData['estado'];
            
            $montoTotal = $apartado->monto_total;
            $montoPagado = $validatedData['monto_pagado'];
            
            if ($montoPagado > $montoTotal) {
                 return response()->json(['message' => 'El monto pagado no puede exceder el monto total.'], 422);
            }

            if ($montoTotal - $montoPagado <= 0 && $newState != 'cancelado') {
                $newState = 'pagado';
            }

            // --- ¡¡¡ARREGLO DEL BUG!!! ---
            // El error 'foreach(null)' es porque la relación es 'items', no 'itemApartados'
            $items = $apartado->items; // <-- BUG FIX (usa la relación 'items()' del modelo)
            // --- FIN DEL ARREGLO ---

            // --- LÓGICA DE STOCK Y VENTAS ---

            // CASO 1: Se CANCELA un apartado (Devolver stock)
            if ($newState == 'cancelado' && $oldState == 'vigente') {
                if($items){ 
                    foreach ($items as $item) {
                        $producto = Producto::find($item->producto_id);
                        if ($producto) $producto->increment('existencias', $item->cantidad);
                    }
                }
            }
            
            // CASO 2: Se PAGA un apartado (Crear Venta)
            if ($newState == 'pagado' && $oldState != 'pagado') {
                
                // Si estaba 'vigente', el stock ya estaba quitado.
                // Si estaba 'cancelado', el stock se devolvió, hay que quitarlo de nuevo.
                if ($oldState == 'cancelado') {
                    if($items){ 
                        foreach ($items as $item) {
                            $producto = Producto::find($item->producto_id);
                            if ($producto) {
                                if ($producto->existencias < $item->cantidad) {
                                    throw new \Exception('Stock insuficiente para pagar el apartado: ' . $producto->nombre);
                                }
                                $producto->decrement('existencias', $item->cantidad);
                            }
                        }
                    }
                }

                // *** NUEVA LÓGICA: Crear un registro de Venta ***
                $venta = Venta::create([
                    'cliente_id' => $apartado->cliente_id,
                    'monto_total' => $apartado->monto_total,
                    'monto_recibido' => $apartado->monto_pagado,
                    'monto_cambio' => $apartado->monto_pagado - $apartado->monto_total, 
                    'metodo_pago' => 'apartado-liquidado', 
                    'users_id' => $apartado->users_id,
                    'created_at' => now(), 
                    'updated_at' => now(),
                ]);

                if($items){ 
                    foreach ($items as $item) {
                        ItemVenta::create([
                            'venta_id' => $venta->id,
                            'producto_id' => $item->producto_id,
                            'cantidad' => $item->cantidad,
                            'precio' => $item->precio, 
                        ]);
                    }
                }
            }

            // CASO 3: Se REACTIVA un apartado (Quitar stock)
            else if ($oldState == 'cancelado' && $newState == 'vigente') {
                if($items){ 
                    foreach ($items as $item) {
                        $producto = Producto::find($item->producto_id);
                        if ($producto) {
                            if ($producto->existencias < $item->cantidad) {
                                throw new \Exception('Stock insuficiente para reactivar el apartado: ' . $producto->nombre);
                            }
                            $producto->decrement('existencias', $item->cantidad);
                        }
                    }
                }
            }

            $apartado->update([
                'monto_pagado' => $montoPagado,
                'monto_restante' => $montoTotal - $montoPagado,
                'fecha_vencimiento' => $validatedData['fecha_vencimiento'],
                'estado' => $newState,
            ]);

            DB::commit();
            return response()->json(['message' => 'Apartado actualizado.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al actualizar apartado: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo actualizar el apartado: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un apartado y devuelve el stock (API para TPV).
     */
    public function apiDestroy(Apartado $apartado)
    {
        DB::beginTransaction();
        try {
            if ($apartado->estado == 'vigente' || $apartado->estado == 'cancelado') {
                $items = $apartado->items; // <-- BUG FIX
                if($items){
                    foreach ($items as $item) {
                        $producto = Producto::find($item->producto_id);
                        if ($producto) {
                            $producto->increment('existencias', $item->cantidad);
                        }
                    }
                }
            }
            
            $apartado->items()->delete(); // <-- BUG FIX
            $apartado->delete();

            DB::commit();
            return response()->json(['message' => 'Apartado eliminado y stock devuelto (si aplicaba).']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar apartado: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo eliminar el apartado.'], 500);
        }
    }
}