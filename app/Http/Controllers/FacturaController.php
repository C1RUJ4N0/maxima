<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // <-- AÑADIR

class FacturaController extends Controller
{
    /**
     * Actualiza una factura (para la vista web Blade).
     */
    public function update(Request $request, Factura $factura)
    {
        $validatedData = $request->validate([
            'estado' => 'required|in:pendiente,pagada',
        ]);

        $factura->update($validatedData);

        return redirect()->back()->with('success', 'Estado de la factura actualizado.');
    }

    // --- INICIO CÓDIGO NUEVO (API) ---

    /**
     * Actualiza una factura (API para TPV).
     */
    public function apiUpdate(Request $request, Factura $factura)
    {
        $validatedData = $request->validate([
            'numero_factura' => 'required|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'fecha_emision' => 'required|date',
            'estado' => 'required|in:pendiente,pagada',
            'imagen_factura' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        try {
            $datosActualizar = [
                'numero_factura' => $validatedData['numero_factura'],
                'monto' => $validatedData['monto'],
                'fecha_emision' => $validatedData['fecha_emision'],
                'estado' => $validatedData['estado'],
            ];
            
            if ($request->hasFile('imagen_factura')) {
                // Borrar la imagen antigua si existe
                if ($factura->imagen_url) {
                    Storage::disk('public')->delete($factura->imagen_url);
                }
                // Guardar la nueva imagen
                $imagenPath = $request->file('imagen_factura')->store('facturas', 'public');
                $datosActualizar['imagen_url'] = $imagenPath;
            }

            $factura->update($datosActualizar);
            
            return response()->json(['message' => 'Factura actualizada.', 'factura' => $factura]);

        } catch (\Exception $e) {
            Log::error("Error al actualizar factura: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo actualizar la factura.'], 500);
        }
    }

    /**
     * Elimina una factura (API para TPV).
     */
    public function apiDestroy(Factura $factura)
    {
        try {
            if ($factura->estado == 'pagada') {
                 return response()->json(['message' => 'No se puede eliminar una factura que ya fue pagada.'], 422);
            }
            
            if ($factura->imagen_url) {
                Storage::disk('public')->delete($factura->imagen_url);
            }
            
            $factura->delete();
            return response()->json(['message' => 'Factura eliminada.']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar factura: " . $e->getMessage());
            return response()->json(['message' => 'No se pudo eliminar la factura.'], 500);
        }
    }
    // --- FIN CÓDIGO NUEVO ---
}