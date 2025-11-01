<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Maxima</title>
    
    {{-- Scripts y Estilos de CDNs y Externos --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- CSS Personalizado --}}
    <link rel="stylesheet" href="{{ asset('css/panel-styles.css') }}">
    
    {{-- Lógica de Alpine.js --}}
    <script src="{{ asset('js/appTPV.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</head>
<body x-data="appTPV()">

    {{-- Pantalla de Carga Inicial --}}
    <div x-show="cargandoInicial" class="flex items-center justify-center min-h-screen" x-cloak>
        <div class="text-center">
            <i class="fas fa-circle-notch fa-spin text-4xl text-sky-600"></i>
            <p class="mt-4 text-lg font-semibold text-gray-700">Inicializando sistema...</p>
        </div>
    </div>

    {{-- Contenido Principal de la App --}}
    <div x-show="!cargandoInicial" x-cloak>
    
        {{-- Notificación Toast --}}
        <div x-show="mostrarNotificacion" x-transition :class="notificacion.exito ? 'bg-green-600' : 'bg-red-600'" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 max-w-sm">
            <span x-text="notificacion.mensaje"></span>
        </div>

        {{-- Todos los Modales (ahora en un archivo separado) --}}
        @include('panel._modals')

        {{-- Encabezado y Navegación de Pestañas --}}
        <header class="bg-sky-50/80 backdrop-blur-sm rounded-lg shadow-xl p-4 mb-4 mx-4 sm:mx-8 mt-4 sticky top-4 z-40">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-sky-200 text-sky-700 flex items-center justify-center rounded-full">
                        <i class="fas fa-water"></i>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-sky-800">Maxima</h1>
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-sky-100 text-sky-700 text-sm px-3 py-1.5 rounded-full hover:bg-sky-200">
                            <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                        </button>
                    </form>
                    <button @click="init()" class="bg-sky-100 text-sky-700 text-sm px-3 py-1.5 rounded-full hover:bg-sky-200">
                        <i class="fas fa-arrows-rotate mr-1" :class="{ 'fa-spin': cargando }"></i> Recargar
                    </button>
                </div>
            </div>
            <nav class="mt-4 border-t pt-2">
                <div class="flex flex-wrap space-x-2 sm:space-x-4">
                    <template x-for="pestaña in pestañas" :key="pestaña.nombre">
                        <button @click="cambiarPestaña(pestaña.nombre)" :class="{'bg-sky-600 text-white': pestañaActiva === pestaña.nombre, 'bg-sky-100 text-sky-700 hover:bg-sky-200': pestañaActiva !== pestaña.nombre}" class="px-3 py-1.5 rounded-full text-sm flex items-center gap-2 mb-2">
                            <i :class="pestaña.icono"></i>
                            <span x-text="pestaña.nombre"></span>
                        </button>
                    </template>
                </div>
            </nav>
        </header>

        {{-- Contenido de las Pestañas (ahora en archivos separados) --}}
        <main class="mx-4 sm:mx-8">
            
            @include('panel.tabs._inventario')

            @include('panel.tabs._estadisticas')

            @include('panel.tabs._proveedores')
            
            @include('panel.tabs._apartados')
            
            @include('panel.tabs._ventas')

        </main>
    </div>
    
</body>
</html>