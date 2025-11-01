@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-xl font-bold text-center text-sky-800">Restablecer Contraseña</div>

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    @error('email')
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Dirección de Email</label>
            <input id="email" type="email" class="block mt-1 w-full border-gray-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-900 focus:outline-none focus:border-sky-900 focus:ring focus:ring-sky-300 disabled:opacity-25 transition">
                Enviar Enlace de Restablecimiento
            </button>
        </div>
    </form>
@endsection