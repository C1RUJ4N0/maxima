@extends('layouts.app')

@section('title', 'Login - Maxima POS')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Maxima POS</h1>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4 text-center">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label class="block text-gray-700 mb-2">Correo electrónico</label>
            <input type="email" name="email" required autofocus
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

            <div class="flex items-center justify-between mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="form-checkbox">
                    <span class="ml-2 text-sm text-gray-700">Recordarme</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-blue-600 text-sm hover:underline">Olvidaste tu contraseña?</a>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Iniciar sesión
            </button>
        </form>

        <div class="mt-6 text-center text-sm">
            ¿No tienes cuenta? 
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Registrarse</a>
        </div>
    </div>
</div>
@endsection
