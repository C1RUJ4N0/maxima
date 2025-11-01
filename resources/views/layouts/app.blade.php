<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Maxima') }}</title>

    {{-- 
      CAMBIO 1: 
      Se cargan CSS y JS juntos en un array.
      Esto arregla que no se carguen los estilos de Tailwind.
    --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Se elimina el link de Bootstrap que estaba causando conflictos --}}
    
    {{-- Para los íconos del menú --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
{{-- CAMBIO 2: Se aplica el color de fondo de tu tema --}}
<body class="bg-maxima-light-blue-100 font-sans antialiased">
    {{-- CAMBIO 3: Se aplica el color de fondo de tu tema --}}
    <div class="flex h-screen bg-maxima-light-blue-100">
        
        {{-- 
          CAMBIO 4: 
          - Se quitó 'bg-gray-800'
          - Se añadió la clase '.bg-maxima-waves' para el fondo de olas
        --}}
        <aside class="w-64 bg-maxima-waves text-white p-4 hidden md:block">
            <h2 class="text-2xl font-bold mb-6 text-center">Maxima</h2>
            <nav>
                <ul>
                    <li class="mb-3">
                        {{-- CAMBIO 5: Se usa el color 'hover' de tu tema y se arregla el link --}}
                        <a href="{{ route('panel.index') }}" class="flex items-center p-2 rounded hover:bg-maxima-hover-blue transition-colors">
                            <i class="fas fa-tachometer-alt w-6 mr-2"></i> Panel
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('registroventas.index') }}" class="flex items-center p-2 rounded hover:bg-maxima-hover-blue transition-colors">
                            <i class="fas fa-cash-register w-6 mr-2"></i> Registro de Ventas
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('estadisticas') }}" class="flex items-center p-2 rounded hover:bg-maxima-hover-blue transition-colors">
                            <i class="fas fa-chart-line w-6 mr-2"></i> Estadísticas
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('proveedores') }}" class="flex items-center p-2 rounded hover:bg-maxima-hover-blue transition-colors">
                            <i class="fas fa-truck w-6 mr-2"></i> Proveedores
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="absolute bottom-0 left-0 w-64 p-4">
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    {{-- CAMBIO 6: Se usa el color de tu tema para el botón de logout --}}
                    <button type="submit" class="w-full flex items-center p-2 rounded bg-maxima-hover-blue hover:bg-maxima-dark-blue transition-colors">
                        <i class="fas fa-sign-out-alt w-6 mr-2"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- Contenido Principal --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- CAMBIO 7: Se aplica el color de fondo de tu tema --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-maxima-light-blue-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Se elimina el script de Bootstrap --}}
    
    {{-- CAMBIO 8: Se elimina el @vite('resources/js/app.js') de aquí (ya se cargó en el head) --}}
    
    {{-- Carga los scripts específicos de cada página (como los gráficos) --}}
    @stack('scripts')
</body>
</html>