@extends('layouts.auth')

@section('content')
    <x-card>
        <h1 class="text-xl font-semibold mb-2">Forgot password</h1>
        <p class="text-sm text-gray-500 mb-4">We’ll email you a reset link.</p>

        @if (session('status'))
            <x-alert type="success" :message="session('status')" />
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <x-label for="email" value="Email" />
                <x-input name="email" type="email" required />
            </div>

            <x-button type="submit" class="w-full justify-center">Send reset link</x-button>
        </form>
    </x-card>
@endsection
