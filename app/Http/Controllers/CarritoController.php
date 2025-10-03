<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto; 

class CarritoController extends Controller
{
    public function agregar(Request $request, $id)
    {
        $producto = Producto::findOrFail($id); 
        
        $carrito = $request->session()->get('carrito', []);

        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
        } else {
            $carrito[$id] = [
                'id' => $id,
                'nombre' => $producto->name, 
                'precio' => $producto->price, 
                'cantidad' => 1
            ];
        }

        $request->session()->put('carrito', $carrito);
        
        return redirect()->back();
    }

    public function eliminar(Request $request, $id)
    {
        $carrito = $request->session()->get('carrito', []);
        
        if (isset($carrito[$id])) {
            unset($carrito[$id]);
        }
        
        $request->session()->put('carrito', $carrito);
        
        return redirect()->back();
    }

    public function actualizar(Request $request, $id)
    {
        $cantidad = (int)$request->input('cantidad', 1);
        $carrito = $request->session()->get('carrito', []);
        
        if (isset($carrito[$id]) && $cantidad > 0) {
            $carrito[$id]['cantidad'] = $cantidad;
        }

        $request->session()->put('carrito', $carrito);
        
        return redirect()->back();
    }

    public function limpiar(Request $request)
    {
        $request->session()->forget('carrito');
        
        return redirect()->back();
    }
}
