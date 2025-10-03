@extends('layouts.app')

@section('title', 'Restablecer contraseña - Maxima POS')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Restablecer contraseña</h1>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label class="block mb-2 text-gray-700">Correo electrónico</label>
            <input type="email" name="email" required value="{{ $email ?? old('email') }}"
                   class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
            @enderror

            <label class="block mb-2 text-gray-700">Nueva contraseña</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
            @error('password')
                <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
            @enderror

            <label class="block mb-2 text-gray-700">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2 mb-4 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                Restablecer contraseña
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Volver al login</a>
        </div>
    </div>
</div>
@endsection
