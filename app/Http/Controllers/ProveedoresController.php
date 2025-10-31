<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Factura;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // <-- AÑADIR

class ProveedoresController extends Controller
{
    // (Métodos Web: index, store, update, destroy ... )
    public function index() { return view('proveedores.index'); }
    public function store(Request $request) { /* Lógica del store web */ }
    public function update(Request $request, Proveedor $proveedor) { /* Lógica del update web */ }
    public function destroy(Proveedor $proveedor) { /* Lógica del destroy web */ }


    // --- MÉTODOS API PARA EL TPV ---

    public function apiIndex()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return response()->json(['proveedores' => $proveedores]);
    }

    public function apiShow($id)
    {
        $proveedor = Proveedor::with('facturas')->findOrFail($id);
        return response()->json($proveedor);
    }

    public function apiStore(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $proveedor = Proveedor::create($validatedData);
        return response()->json($proveedor, 201);
    }

    // --- MÉTODO MODIFICADO PARA SUBIR IMAGEN ---
    public function apiStoreFactura(Request $request, Proveedor $proveedor)
    {
        $validatedData = $request->validate([
            'numero_factura' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'fecha_emision' => 'required|date',
            'imagen_factura' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación de imagen
        ]);

        $imagenPath = null;
        if ($request->hasFile('imagen_factura')) {
            // Guardar en 'storage/app/public/facturas'
            $imagenPath = $request->file('imagen_factura')->store('facturas', 'public');
        }

        $factura = $proveedor->facturas()->create([
            'numero_factura' => $validatedData['numero_factura'],
            'monto' => $validatedData['monto'],
            'fecha_emision' => $validatedData['fecha_emision'],
            'estado' => 'pendiente',
            'imagen_url' => $imagenPath, // Guardar la ruta
        ]);

        return response()->json($factura, 201);
    }
    // --- FIN DE LA MODIFICACIÓN ---

    public function apiUpdate(Request $request, Proveedor $proveedor)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'descripcion' => 'nullable|string',
        ]);

        try {
            $proveedor->update($validatedData);
            return response()->json(['message' => 'Proveedor actualizado.', 'proveedor' => $proveedor]);
        } catch (\Exception $e) {
            Log::error("Error al actualizar proveedor: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo actualizar el proveedor.'], 500);
        }
    }

    public function apiDestroy(Proveedor $proveedor)
    {
        DB::beginTransaction();
        try {
            // Borrar imágenes asociadas
            foreach ($proveedor->facturas as $factura) {
                if ($factura->imagen_url) {
                    Storage::disk('public')->delete($factura->imagen_url);
                }
            }
            
            $proveedor->facturas()->delete();
            $proveedor->delete();

            DB::commit();
            return response()->json(['message' => 'Proveedor y sus facturas eliminados.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar proveedor: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo eliminar el proveedor.'], 500);
        }
    }
}