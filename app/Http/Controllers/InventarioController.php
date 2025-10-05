<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Cliente; // Asegúrate de importar el modelo Cliente

class InventarioController extends Controller
{
    /**
     * Devuelve todos los clientes para el TPV.
     * ESTA ES LA FUNCIÓN QUE FALTABA Y CAUSABA EL ERROR FATAL.
     */
    public function getClientes()
    {
        // Obtener todos los clientes, incluyendo el "Cliente General"
        $clientes = Cliente::orderBy('nombre')->get();
        return response()->json(['clientes' => $clientes]);
    }

    /**
     * Busca productos según el término de búsqueda.
     */
    public function searchProductos(Request $request)
    {
        $query = $request->input('q', '');
        if (empty($query)) {
            return response()->json(['productos' => []]);
        }

        $productos = Producto::where('nombre', 'LIKE', "%{$query}%")
            ->orWhere('id', $query) // Permite buscar por ID también
            ->limit(20)
            ->get();
        
        return response()->json(['productos' => $productos]);
    }

    /**
     * Almacena un nuevo producto en la base de datos.
     */
    public function storeProducto(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:productos',
            'precio' => 'required|numeric|min:0',
            'existencias' => 'required|integer|min:0',
        ]);

        $producto = Producto::create($validatedData);

        return response()->json($producto, 201);
    }

    /**
     * Almacena un nuevo cliente en la base de datos.
     */
    public function storeCliente(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:clientes',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $cliente = Cliente::create($validatedData);

        return response()->json(['cliente' => $cliente], 201);
    }
}