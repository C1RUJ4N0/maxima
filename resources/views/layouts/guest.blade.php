<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Maxima') }}</title>
    
    {{-- Scripts y Estilos --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Fuentes --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Estilos de Olas y Fondo (Igual que el panel) --}}
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #e0f2fe; /* bg-sky-100 */ 
            min-height: 100vh;
            /* Olas de mar minimalistas (CAPA 1 - MÁS CLARA) */
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23f0f9ff' fill-opacity='0.7' d='M0,224L48,234.7C96,245,192,267,288,261.3C384,256,480,224,576,197.3C672,171,768,149,864,160C960,171,1056,213,1152,218.7C1248,224,1344,192,1392,176L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E"), 
                              /* Olas de mar minimalistas (CAPA 2 - ORIGINAL) */
                              url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23bae6fd' fill-opacity='1' d='M0,192L48,176C96,160,192,128,288,133.3C384,139,480,181,576,202.7C672,224,768,224,864,208C960,192,1056,160,1152,149.3C1248,139,1344,149,1392,154.7L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: bottom, bottom;
            background-size: 100% auto, 100% auto;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="font-['Poppins'] text-gray-900 antialiased">
    {{-- Se eliminó bg-gray-100 para usar el fondo del body --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-sky-200 text-sky-700 flex items-center justify-center rounded-full">
                    <i class="fas fa-water"></i>
                </div>
                <h1 class="text-2xl font-bold text-sky-800">Maxima</h1>
            </a>
        </div>

        {{-- Tarjeta de login con estilo del panel --}}
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-sky-50 shadow-xl overflow-hidden sm:rounded-xl">
            @yield('content')
        </div>
    </div>
</body>
</html>
