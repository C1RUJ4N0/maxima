<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Comprueba si el usuario está logueado Y si NO es admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            
            // 2. SI es una petición de API (como las de tu TPV)
            if ($request->expectsJson() || $request->is('api/*')) {
                // Devuelve un error JSON, que Alpine.js SÍ entiende
                return response()->json(['message' => 'Acción no autorizada.'], 403);
            }

            // 3. SI es una petición Web normal (como las otras vistas)
            // Redirige al panel con un error
            return redirect()->route('panel.index')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // 4. Si es admin, déjalo pasar
        return $next($request);
    }
}