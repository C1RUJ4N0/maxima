<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes', compact('clientes'));
    }

    public function almacenar(Request $request)
    {
        $datosValidados = $request->validate([
            'nombre' => 'required|string|max:255',
            'numero_telefono' => 'nullable|string|max:255',
        ]);

        try {
            Cliente::create($datosValidados);
            return response()->json(['success' => true, 'message' => 'Cliente creado con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error al crear el cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el cliente.'], 500);
        }
    }

    public function actualizar(Request $request, Cliente $cliente)
    {
        $datosValidados = $request->validate([
            'nombre' => 'required|string|max:255',
            'numero_telefono' => 'nullable|string|max:255',
        ]);

        try {
            $cliente->update($datosValidados);
            return response()->json(['success' => true, 'message' => 'Cliente actualizado con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar el cliente.'], 500);
        }
    }

    public function eliminar(Cliente $cliente)
    {
        try {
            $cliente->delete();
            return response()->json(['success' => true, 'message' => 'Cliente eliminado con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el cliente.'], 500);
        }
    }
}
