<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\ItemVenta;

/**
 * Controlador para la gestión del Inventario y funciones de Venta como API.
 * NOTA: Los métodos de venta (obtenerClientes, finalizarVenta) deberían
 * considerarse para ser movidos a un VentaController o PanelController
 * en una refactorización futura para mantener el principio de una sola responsabilidad.
 */
class InventarioController extends Controller
{
    /**
     * Muestra la vista principal del inventario.
     */
    public function index(): View
    {
        return view('inventario.index');
    }

    /**
     * Obtiene todos los productos del inventario (API Endpoint).
     */
    public function obtenerProductos()
    {
        // Obtiene todos los productos
        $productos = Producto::all();
        
        return response()->json(['productos' => $productos]);
    }

    /**
     * Agrega un nuevo producto al inventario (POST - Mismo que 'guardar').
     * Corregido el nombre del método a 'guardar' para que coincida con la ruta 'inventario.guardar'.
     */
    public function guardar(Request $request)
    {
        // 1. Validar los datos de entrada. 
        $datosValidados = $request->validate([
            // CORREGIDO: 'products' cambiado a 'productos' para la validación única.
            'nombre' => 'required|string|max:255|unique:productos,nombre', 
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
        ]);

        try {
            // 2. Crear el producto
            $producto = Producto::create($datosValidados);

            return response()->json(['mensaje' => 'Producto agregado con éxito', 'producto' => $producto], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar el producto: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Obtiene la lista de clientes (API Endpoint).
     */
    public function obtenerClientes()
    {
        $clientes = Cliente::all();
        
        return response()->json(['clientes' => $clientes]);
    }

    /**
     * Finaliza la venta, actualiza el stock y crea los registros de venta (API Endpoint).
     */
    public function finalizarVenta(Request $request)
    {
        // 1. Validar la estructura de la solicitud
        $request->validate([
            'items' => 'required|array|min:1',
            // CORREGIDO: 'products' cambiado a 'productos'
            'items.*.id' => 'required|exists:productos,id', 
            'items.*.cantidad' => 'required|integer|min:1',
            // CORREGIDO: 'clients' cambiado a 'clientes'
            'clienteIdSeleccionado' => 'required|exists:clientes,id', 
            'montoTotal' => 'required|numeric|min:0',
        ]);

        $itemsVenta = $request->input('items');
        $montoTotal = $request->input('montoTotal');
        $clienteId = $request->input('clienteIdSeleccionado');
        $productosEnVenta = collect($itemsVenta);
        
        // Usar una transacción de base de datos para asegurar atomicidad
        DB::beginTransaction();

        try {
            // 2. Verificar Stock Suficiente y Preparar para Actualización
            $productosIds = $productosEnVenta->pluck('id')->toArray();
            
            // Bloquea las filas para evitar condiciones de carrera (concurrency)
            $inventarioActual = Producto::whereIn('id', $productosIds)
                                         ->lockForUpdate() 
                                         ->get()
                                         ->keyBy('id');

            foreach ($productosEnVenta as $item) {
                $producto = $inventarioActual->get($item['id']);
                
                // Si el producto no existe o el stock es insuficiente, lanza un error.
                if (!$producto || $producto->stock < $item['cantidad']) {
                    DB::rollBack();
                    $nombreProducto = $producto ? $producto->nombre : "ID: {$item['id']}";
                    return response()->json([
                        'mensaje' => 'Error de stock',
                        'error' => ['stock' => ["Stock insuficiente para el producto '{$nombreProducto}'. Stock actual: " . ($producto ? $producto->stock : '0')]]
                    ], 409); // 409 Conflict
                }
            }
            
            // 3. Crear el Registro de Venta
            // Mantenemos estas columnas en inglés por si la tabla 'ventas' sigue la convención de Laravel.
            $venta = Venta::create([
                'client_id' => $clienteId,
                'total_amount' => $montoTotal, 
            ]);

            // 4. Crear los Registros de Artículos de Venta y Actualizar el Stock
            foreach ($productosEnVenta as $item) {
                $producto = $inventarioActual->get($item['id']);
                
                // Crea el artículo de la venta (ItemVenta)
                ItemVenta::create([
                    'id_venta' => $venta->id,
                    'id_producto' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                ]);

                // Actualiza el stock
                $producto->decrement('stock', $item['cantidad']);
            }

            DB::commit(); // Confirma todos los cambios

            return response()->json([
                'mensaje' => 'Venta finalizada con éxito',
                'id_venta' => $venta->id,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Deshace todos los cambios en caso de cualquier error
            return response()->json(['error' => 'Error interno de la transacción: ' . $e->getMessage()], 500);
        }
    }
}
