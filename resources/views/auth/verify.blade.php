@extends('layouts.guest')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-center">
        <div class="w-full max-w-md">
            <div class="bg-white shadow-md rounded-lg px-8 pt-6 pb-8 mb-4">
                <div class="mb-4 text-xl font-bold text-center">{{ __('Verify Your Email Address') }}</div>

                @if (session('resent'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif

                <p class="text-gray-700 mb-4">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                </p>
                <p class="text-gray-700">
                    {{ __('If you did not receive the email') }},
                    <form class="inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="text-blue-500 hover:text-blue-700 focus:outline-none">
                            {{ __('click here to request another') }}
                        </button>.
                    </form>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection