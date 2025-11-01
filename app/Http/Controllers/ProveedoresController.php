<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor; // <--- AÑADIDO
use App\Models\Factura;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProveedoresController extends Controller
{
    // --- ESTA ES LA CORRECCIÓN ---
    public function index()
    {
        // Añadimos la lógica para buscar los proveedores
        $proveedores = Proveedor::with('facturas')->get();
        
        // Los pasamos a la vista usando compact()
        return view('proveedores.index', compact('proveedores'));
    }
    // --- FIN DE LA CORRECCIÓN ---

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Proveedor::create($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado con éxito.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $proveedor->update($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado con éxito.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado con éxito.');
    }

    // --- MÉTODOS DE LA API (ya estaban bien) ---
    public function apiIndex()
    {
        $proveedores = Proveedor::get();
        return response()->json(['proveedores' => $proveedores]);
    }

    public function apiShow(Proveedor $proveedor)
    {
        $proveedor->load('facturas');
        return response()->json($proveedor);
    }
    
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $proveedor = Proveedor::create($validated);
        return response()->json($proveedor, 201);
    }
    
    public function apiUpdate(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $proveedor->update($validated);
        return response()->json(['message' => 'Proveedor actualizado.', 'proveedor' => $proveedor]);
    }
    
    public function apiDestroy(Proveedor $proveedor)
    {
        try {
            $proveedor->facturas()->each(function ($factura) {
                if ($factura->imagen_url) {
                    Storage::disk('public')->delete($factura->imagen_url);
                }
                $factura->delete();
            });
            $proveedor->delete();
            return response()->json(['message' => 'Proveedor y sus facturas eliminados.']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar proveedor: " . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el proveedor.'], 500);
        }
    }

    public function apiStoreFactura(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'numero_factura' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0',
            'fecha_emision' => 'required|date',
            'imagen_factura' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('imagen_factura')) {
            $path = $request->file('imagen_factura')->store('facturas', 'public');
        }

        $factura = $proveedor->facturas()->create([
            'numero_factura' => $validated['numero_factura'],
            'monto' => $validated['monto'],
            'fecha_emision' => $validated['fecha_emision'],
            'imagen_url' => $path,
            'estado' => 'pendiente'
        ]);

        return response()->json($factura, 201);
    }
}