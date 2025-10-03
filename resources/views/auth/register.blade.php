@extends('layouts.app')

@section('title', 'Registro - Maxima POS')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Crear cuenta</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label class="block text-gray-700 mb-2">Nombre completo</label>
            <input type="text" name="name" required
                   class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                   value="{{ old('name') }}">
            @error('name')
                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
            @enderror

            <label class="block text-gray-700 mb-2">Correo electrónico</label>
            <input type="email" name="email" required
                   class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                   value="{{ old('email') }}">
            @error('email')
                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
            @enderror

            <label class="block text-gray-700 mb-2">Contraseña</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
            @enderror

            <label class="block text-gray-700 mb-2">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Registrarse
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            ¿Ya tienes cuenta? 
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Iniciar sesión</a>
        </div>
    </div>
</div>
@endsection
