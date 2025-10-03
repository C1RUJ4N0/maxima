<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedoresController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::all();
        // CAMBIO: Devolver JSON con los proveedores
        return response()->json(['proveedores' => $proveedores]);
    }
}