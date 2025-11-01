@extends('layouts.guest')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <h2 class="text-2xl font-bold text-center mb-6 text-sky-800">Crear Cuenta</h2>

        @error('email')
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ $message }}
            </div>
        @enderror

        <div>
            <label for="name" class="block font-medium text-sm text-gray-700">Nombre</label>
            <input id="name" class="block mt-1 w-full border-gray-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm" type="text" name="name" value="{{ old('name') }}" required autofocus />
        </div>
        
        <div class="mt-4">
            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
            <input id="email" class="block mt-1 w-full border-gray-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm" type="email" name="email" value="{{ old('email') }}" required />
        </div>
        
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Contraseña</label>
            <input id="password" class="block mt-1 w-full border-gray-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm" type="password" name="password" required />
        </div>
        
        <div class="mt-4">
            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirmar Contraseña</label>
            <input id="password_confirmation" class="block mt-1 w-full border-gray-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm" type="password" name="password_confirmation" required />
        </div>
        
        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-sky-600 hover:text-sky-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500" href="{{ route('login') }}">
                ¿Ya estás registrado?
            </a>
            
            <button type="submit" class="ms-4 inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-900 focus:outline-none focus:border-sky-900 focus:ring focus:ring-sky-300 disabled:opacity-25 transition">
                Registrar
            </button>
        </div>
    </form>
@endsection