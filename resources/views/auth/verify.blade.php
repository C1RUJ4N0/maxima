@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-xl font-bold text-center text-sky-800">Verifica tu Dirección de Email</div>

    @if (session('resent'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            Un nuevo enlace de verificación ha sido enviado a tu dirección de email.
        </div>
    @endif

    <p class="text-gray-700 mb-4">
        Antes de continuar, por favor revisa tu email para ver el enlace de verificación.
    </p>
    <p class="text-gray-700">
        Si no recibiste el email,
        <form class="inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="underline text-sm text-sky-600 hover:text-sky-900 focus:outline-none">
                haz clic aquí para solicitar otro
            </button>.
        </form>
    </p>
@endsection