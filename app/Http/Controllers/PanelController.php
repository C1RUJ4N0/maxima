<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanelController extends Controller
{
    /**
     * Muestra el panel principal del TPV.
     */
    public function index()
    {
        // Esta función simplemente carga la vista que contiene tu aplicación de Alpine.js
        return view('panel.index');
    }
}