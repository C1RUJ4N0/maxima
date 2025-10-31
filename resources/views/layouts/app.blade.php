<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Maxima') }}</title>

    {{-- Carga los estilos CSS de la aplicación (Tailwind) --}}
    @vite('resources/css/app.css')
    
    <!-- *** AÑADIR ESTO: BOOTSTRAP CSS *** -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    {{-- Para los íconos del menú --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen bg-gray-200">
        
        {{-- Menú Lateral (Tu código original de Tailwind) --}}
        <aside class="w-64 bg-gray-800 text-white p-4 hidden md:block">
            <h2 class="text-2xl font-bold mb-6 text-center">Maxima</h2>
            <nav>
                <ul>
                    <li class="mb-3">
                        <a href="{{-- route('panel.index') --}}" class="flex items-center p-2 rounded hover:bg-gray-700 transition-colors">
                            <i class="fas fa-tachometer-alt w-6 mr-2"></i> Panel
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('registroventas.index') }}" class="flex items-center p-2 rounded hover:bg-gray-700 transition-colors">
                            <i class="fas fa-cash-register w-6 mr-2"></i> Registro de Ventas
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('estadisticas') }}" class="flex items-center p-2 rounded hover:bg-gray-700 transition-colors">
                            <i class="fas fa-chart-line w-6 mr-2"></i> Estadísticas
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('proveedores') }}" class="flex items-center p-2 rounded hover:bg-gray-700 transition-colors">
                            <i class="fas fa-truck w-6 mr-2"></i> Proveedores
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="absolute bottom-0 left-0 w-64 p-4">
                 <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center p-2 rounded bg-red-500 hover:bg-red-700 transition-colors">
                        <i class="fas fa-sign-out-alt w-6 mr-2"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- Contenido Principal --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- *** AÑADIR ESTO: BOOTSTRAP JS (Para que funcionen los modales) *** -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    {{-- Carga el JavaScript principal de tu aplicación --}}
    @vite('resources/js/app.js')
    
    {{-- Carga los scripts específicos de cada página (como los gráficos) --}}
    @stack('scripts')
</body>
</html>
