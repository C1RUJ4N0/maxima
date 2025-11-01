@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-xl font-bold text-center text-sky-800">Restablecer Contraseña</div>

    @error('email')
        <div class="mb-4 font-medium text-sm text-red-600">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Dirección de Email</label>
            <input id="email" type="email" class="block mt-1 w-full border-gray-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Contraseña</dlabel>
            <input id="password" type="password" class="block mt-1 w-full border-gray-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm" name="password" required autocomplete="new-password">
        </div>

        <div class="mb-6">
            <label for="password-confirm" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Contraseña</label>
            <input id="password-confirm" type="password" class="block mt-1 w-full border-gray-300 focus:border-sky-500 focus:ring-sky-500 rounded-md shadow-sm" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-900 focus:outline-none focus:border-sky-900 focus:ring focus:ring-sky-300 disabled:opacity-25 transition">
                Restablecer Contraseña
            </button>
        </div>
    </form>
@endsection