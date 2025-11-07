{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.auth')

@section('title', 'Sign in to Siatex Docs')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-[#f2f8fd] dark:from-gray-900 dark:to-gray-800 px-4">
        <div class="w-full max-w-md">
            <x-card
                class="shadow-2xl border-0 backdrop-blur-xl  dark:bg-gray-900/95 ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="flex  flex-col items-center justify-center group">
                    <!-- Main Logo -->
                    <div class="p-3 my-3 bg-[#f2f8fd] rounded-full">
                        <img src="{{ asset('assets/doc-logo.png') }}" alt="Siatex Docs Logo"
                            class="w-15 h-15 md:w-15 md:h-15 object-contain ">
                    </div>

                    {{--  <!-- Optional: Gradient Text Below Logo (uncomment if you want text too) -->
                    <h1
                        class="mt-4 text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        Siatex Docs
                    </h1> --}}
                </div>

                @if ($errors->any())
                    <x-alert type="error" class="mb-6">
                        <strong>Oops!</strong> {{ $errors->first() }}
                    </x-alert>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div class="space-y-2">
                        <x-label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300 ">Email
                            Address</x-label>
                        <x-input name="email" type="email" required autocomplete="email" placeholder="you@siatex.com"
                            class="w-full px-4 py-3 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-[#c2e0f5] focus:ring-4 focus:ring-indigo-500/20 transition-all"
                            :value="old('email')" />
                    </div>

                    <!-- Password + Toggle -->
                    <div class="space-y-2">
                        <x-label for="password"
                            class="text-sm font-medium text-gray-700 dark:text-gray-300">Password</x-label>
                        <div x-data="{ show: false }" class="relative">
                            <x-input name="password" id="password" x-bind:type="show ? 'text' : 'password'" required
                                autocomplete="current-password" placeholder="••••••••••••"
                                class="w-full px-4 py-3 pr-12 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition-all" />
                            <button type="button" @click="show = !show"
                                class="cursor-pointer absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                <span x-show="!show" x-transition class="text-xs font-medium">Show</span>
                                <span x-show="show" x-transition class="text-xs font-medium">Hide</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-sm">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-gray-700 dark:text-gray-300">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}"
                            class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 font-medium hover:underline">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit -->
                    <x-button type="submit"
                        class="w-full py-4 text-base font-semibold rounded-xl bg-[#1a72af] text-white shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                        <span>Sign In</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </x-button>

                    {{-- No public registration link (registration is admin-only) --}}
                </form>
            </x-card>

            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    © {{ date('Y') }} Siatex Docs. Crafted with <span class="text-red-500">♥</span>.
                </p>
            </div>
        </div>
    </div>
@endsection
