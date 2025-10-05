<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Egreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacturaController extends Controller
{
    /**
     * Actualiza el estado de una factura. Si se marca como "pagada",
     * crea automáticamente un registro de Egreso.
     */
    public function update(Request $request, Factura $factura)
    {
        $request->validate([
            'estado' => 'required|string|in:pendiente,pagada',
        ]);

        try {
            // Solo actuar si el estado cambia de "pendiente" a "pagada"
            if ($factura->estado === 'pendiente' && $request->estado === 'pagada') {
                $factura->estado = 'pagada';
                $factura->save();

                // Crear el Egreso correspondiente
                Egreso::create([
                    'descripcion' => 'Pago de factura N°' . $factura->numero_factura . ' a ' . $factura->proveedor->nombre,
                    'monto' => $factura->monto,
                ]);

                return back()->with('success', 'Factura marcada como pagada y egreso registrado correctamente.');
            }

            // Manejar el caso de que se revierta un pago
            if ($factura->estado === 'pagada' && $request->estado === 'pendiente') {
                $factura->estado = 'pendiente';
                $factura->save();
                // Opcional: Aquí se podría implementar la lógica para eliminar el egreso asociado si es necesario.
                return back()->with('success', 'El estado de la factura se ha revertido a pendiente.');
            }

        } catch (\Exception $e) {
            Log::error('Error al actualizar factura y registrar egreso: ' . $e->getMessage());
            return back()->with('error', 'Hubo un error al procesar la solicitud.');
        }

        return back()->with('info', 'No se realizaron cambios en la factura.');
    }
}