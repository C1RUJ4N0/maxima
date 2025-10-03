<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Maxima')</title>
    <!-- Incluye tus estilos de Vite (Tailwind CSS) -->
    @vite('resources/css/app.css') 
    
    <!-- Alpine.js CDN -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Font Awesome (Usando tu versión) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        /* Estilos globales y tipografía de Inter (añadidos para consistencia) */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Usando el color de fondo de la refactorización para un mejor look */
            min-height: 100vh;
        }
        
        /* Ocultar elementos Alpine */
        [x-cloak] { display: none !important; }

        /* Estilos base para botones del POS (Convertidos a clases directas de Tailwind) */
        /* La directiva @apply no funciona en un bloque <style> inline, por eso se reemplazan las clases. */
        .btn-header {
            padding-left: 1rem; /* px-4 */
            padding-right: 1rem; /* px-4 */
            padding-top: 0.5rem; /* py-2 */
            padding-bottom: 0.5rem; /* py-2 */
            border-radius: 0.75rem; /* rounded-xl */
            background-color: #e0e7ff; /* bg-indigo-100 */
            color: #4338ca; /* text-indigo-700 */
            font-weight: 500; /* font-medium */
            transition: background-color 150ms ease-in-out; /* transition-colors */
            display: flex; /* flex */
            align-items: center; /* items-center */
            gap: 0.25rem; /* gap-1 */
        }
        .btn-header:hover {
            background-color: #c7d2fe; /* hover:bg-indigo-200 */
        }

        .btn-primary-sm {
            padding-left: 1rem; /* px-4 */
            padding-right: 1rem; /* px-4 */
            padding-top: 0.5rem; /* py-2 */
            padding-bottom: 0.5rem; /* py-2 */
            background-color: #4f46e5; /* bg-indigo-600 */
            color: white; /* text-white */
            border-radius: 0.5rem; /* rounded-lg */
            transition: background-color 150ms ease-in-out; /* transition-colors */
        }
        .btn-primary-sm:hover {
            background-color: #4338ca; /* hover:bg-indigo-700 */
        }
        
        .btn-success-lg {
            width: 100%; /* w-full */
            padding-left: 1rem; /* px-4 */
            padding-right: 1rem; /* px-4 */
            padding-top: 0.75rem; /* py-3 */
            padding-bottom: 0.75rem; /* py-3 */
            background-color: #10b981; /* bg-green-600 */
            color: white; /* text-white */
            border-radius: 0.75rem; /* rounded-xl */
            font-weight: 700; /* font-bold */
            font-size: 1.125rem; /* text-lg */
            transition: background-color 150ms ease-in-out; /* transition-colors */
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); /* shadow-lg */
        }
        .btn-success-lg:hover:not(:disabled) {
            background-color: #059669; /* hover:bg-green-700 */
        }
        .btn-success-lg:disabled {
            background-color: #9ca3af; /* disabled:bg-gray-400 */
            cursor: not-allowed;
        }

        .btn-warning-lg {
            width: 100%; /* w-full */
            padding-left: 1rem; /* px-4 */
            padding-right: 1rem; /* px-4 */
            padding-top: 0.75rem; /* py-3 */
            padding-bottom: 0.75rem; /* py-3 */
            background-color: #f59e0b; /* bg-yellow-500 */
            color: white; /* text-white */
            border-radius: 0.75rem; /* rounded-xl */
            font-weight: 700; /* font-bold */
            font-size: 1.125rem; /* text-lg */
            transition: background-color 150ms ease-in-out; /* transition-colors */
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); /* shadow-lg */
        }
        .btn-warning-lg:hover:not(:disabled) {
            background-color: #d97706; /* hover:bg-yellow-600 */
        }
        .btn-warning-lg:disabled {
            background-color: #9ca3af; /* disabled:bg-gray-400 */
            cursor: not-allowed;
        }

        .btn-info-lg {
            width: 100%; /* w-full */
            padding-left: 1rem; /* px-4 */
            padding-right: 1rem; /* px-4 */
            padding-top: 0.75rem; /* py-3 */
            padding-bottom: 0.75rem; /* py-3 */
            background-color: #3b82f6; /* bg-blue-500 */
            color: white; /* text-white */
            border-radius: 0.75rem; /* rounded-xl */
            font-weight: 700; /* font-bold */
            font-size: 1.125rem; /* text-lg */
            transition: background-color 150ms ease-in-out; /* transition-colors */
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); /* shadow-lg */
        }
        .btn-info-lg:hover:not(:disabled) {
            background-color: #2563eb; /* hover:bg-blue-600 */
        }
        .btn-info-lg:disabled {
            background-color: #9ca3af; /* disabled:bg-gray-400 */
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Barra de Navegación (Mantenida de tu código) -->
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-gray-800">
            Maxima
        </div>
        <div class="flex items-center space-x-6">
            <a href="{{ route('panel.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
            <a href="{{ route('estadisticas.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-chart-line"></i>
                <span>Estadísticas</span>
            </a>
            <a href="{{ route('inventario.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-box-open"></i>
                <span>Inventario</span>
            </a>
            <a href="{{ route('apartados.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-calendar-check"></i>
                <span>Apartados</span>
            </a>
            <a href="{{ route('proveedores.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-truck"></i>
                <span>Proveedores</span>
            </a>
        </div>
        <div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </nav>

    
    <!-- Contenido principal -->
    <div class="container mx-auto p-6 lg:p-8"> <!-- Ajuste de padding para mejor distribución -->
        @yield('content')
    </div>

    <!-- Se cambió @yield('scripts') por @stack('scripts') para que funcione con la refactorización POS -->
    @stack('scripts')
</body>
</html>
