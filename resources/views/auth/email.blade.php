@extends('layouts.app')

@section('title', 'Recuperar contraseña - Maxima POS')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Recuperar contraseña</h1>
        <p class="text-sm text-gray-600 mb-6 text-center">Ingresa tu correo para recibir el link de recuperación</p>

        @if (session('status'))
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4 text-center">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label class="block mb-2 text-gray-700">Correo electrónico</label>
            <input type="email" name="email" required
                   class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                   value="{{ old('email') }}">
            @error('email')
                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
            @enderror

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Enviar link de recuperación
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Volver al login</a>
        </div>
    </div>
</div>
@endsection
