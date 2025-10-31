<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Support\Facades\Log;

class InventarioController extends Controller
{
    // Método para la vista Blade separada (si existe)
    public function index()
    {
        return view('inventario.index');
    }

    // --- MÉTODOS API PARA EL TPV ---

    public function apiIndex()
    {
        // (Este método parece no usarse en tu panel TPV, pero lo mantengo)
        $productos = Producto::orderBy('nombre')->get();
        return response()->json($productos);
    }

    public function getClientes()
    {
        // --- LÓGICA MODIFICADA ---
        $clientes = Cliente::orderBy('nombre')->get();
        
        $clienteGeneral = null;
        $clienteGeneralId = null;

        // Buscamos y extraemos "Cliente General"
        $clientes = $clientes->filter(function ($cliente) use (&$clienteGeneral, &$clienteGeneralId) {
            if (strtolower($cliente->nombre) === 'cliente general') {
                $clienteGeneral = $cliente;
                $clienteGeneralId = $cliente->id;
                return false; // No incluirlo en la lista principal todavía
            }
            return true; // Incluir todos los demás
        });

        // Si se encontró "Cliente General", lo añadimos al principio de la colección
        if ($clienteGeneral) {
            $clientes->prepend($clienteGeneral);
        }
        // --- FIN DE LA MODIFICACIÓN ---

        return response()->json([
            'clientes' => $clientes->values(), // .values() para reindexar el array
            'clienteGeneralId' => $clienteGeneralId 
        ]);
    }

    public function searchProductos(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return response()->json(['productos' => []]);
        }

        $productos = Producto::where('nombre', 'LIKE', "%{$query}%")
                            ->orWhere('id', $query) // Asumiendo que 'id' es numérico, o puedes castear
                            ->take(10)
                            ->get();
        
        return response()->json(['productos' => $productos]);
    }

    public function storeProducto(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:productos',
            'precio' => 'required|numeric|min:0',
            'existencias' => 'required|integer|min:0',
        ]);

        try {
            $producto = Producto::create($validatedData);
            return response()->json($producto, 201);
        } catch (\Exception $e) {
            Log::error("Error al crear producto: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo crear el producto.'], 500);
        }
    }

    public function storeCliente(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $cliente = Cliente::create($validatedData);
            return response()->json(['message' => 'Cliente añadido', 'cliente' => $cliente], 201);
        } catch (\Exception $e) {
            Log::error("Error al crear cliente: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo crear el cliente.'], 500);
        }
    }

    public function apiUpdateProducto(Request $request, Producto $producto)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:productos,nombre,' . $producto->id,
            'precio' => 'required|numeric|min:0',
            'existencias' => 'required|integer|min:0',
        ]);

        try {
            $producto->update($validatedData);
            return response()->json(['message' => 'Producto actualizado.', 'producto' => $producto]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar producto: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo actualizar el producto.'], 500);
        }
    }

    public function apiDestroyProducto(Producto $producto)
    {
        try {
            // (Aquí deberías añadir lógica para verificar si el producto está en ventas/apartados)
            $producto->delete();
            return response()->json(['message' => 'Producto eliminado.']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar producto: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo eliminar el producto.'], 500);
        }
    }
}