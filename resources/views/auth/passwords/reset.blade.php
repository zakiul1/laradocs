@extends('layouts.auth')

@section('content')
    <x-card>
        <h1 class="text-xl font-semibold mb-4">Reset password</h1>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <x-label for="password" value="New password" />
                <x-input name="password" type="password" required />
            </div>

            <div>
                <x-label for="password_confirmation" value="Confirm new password" />
                <x-input name="password_confirmation" type="password" required />
            </div>

            <x-button type="submit" class="w-full justify-center">Update password</x-button>
        </form>
    </x-card>
@endsection
