@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Page header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Register Admin</h1>
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2">
                    <path d="M15 18l-6-6 6-6" />
                </svg>
                Back
            </a>
        </div>

        <!-- Card -->
        <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur p-6 shadow-sm">
            <!-- Success / status -->
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6" x-data="{ showPwd: false, showPwd2: false, strength: 0 }"
                x-init="$watch('password', v => {})">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                        class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-400
                              focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10" />
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-400
                              focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10" />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Passwords --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div x-data="{ pwd: '' }"
                        @input="strength = [
                        (pwd.length >= 8) ? 1 : 0,
                        /[A-Z]/.test(pwd) ? 1 : 0,
                        /[0-9]/.test(pwd) ? 1 : 0,
                        /[^A-Za-z0-9]/.test(pwd) ? 1 : 0
                    ].reduce((a,b)=>a+b,0)">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-2 relative">
                            <input id="password" name="password" :type="showPwd ? 'text' : 'password'" x-model="pwd"
                                required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pr-10
                                      text-gray-900 placeholder:text-gray-400 focus:border-gray-900
                                      focus:outline-none focus:ring-2 focus:ring-gray-900/10" />
                            <button type="button" @click="showPwd = !showPwd"
                                class="absolute inset-y-0 right-2 my-auto inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-500 hover:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        <!-- Strength -->
                        <div class="mt-2 h-1.5 w-full rounded-full bg-gray-100">
                            <div class="h-1.5 rounded-full transition-all"
                                :class="{
                                    'bg-red-500 w-1/4': strength === 1,
                                    'bg-yellow-500 w-2/4': strength === 2,
                                    'bg-amber-600 w-3/4': strength === 3,
                                    'bg-emerald-600 w-full': strength >= 4
                                }">
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Use at least 8 chars, with a number & symbol.</p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                            Password</label>
                        <div class="mt-2 relative">
                            <input id="password_confirmation" name="password_confirmation"
                                :type="showPwd2 ? 'text' : 'password'" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pr-10
                                      text-gray-900 placeholder:text-gray-400 focus:border-gray-900
                                      focus:outline-none focus:ring-2 focus:ring-gray-900/10" />
                            <button type="button" @click="showPwd2 = !showPwd2"
                                class="absolute inset-y-0 right-2 my-auto inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-500 hover:text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <div class="mt-2 relative">
                        <select id="role" name="role"
                            class="w-full appearance-none rounded-xl border border-gray-300 bg-white px-4 py-2.5
                                    text-gray-900 focus:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-900/10">
                            <option value="admin" {{ old('role', 'admin') === 'admin' ? 'selected' : '' }}>admin</option>
                            <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>super_admin
                            </option>
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-500"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.186l3.71-3.955a.75.75 0 111.08 1.04l-4.24 4.52a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" />
                        </svg>
                    </div>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active switch --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3" x-data="{ on: {{ old('is_active', 1) ? 'true' : 'false' }} }">
                        <input type="hidden" name="is_active" :value="on ? 1 : 0">
                        <button type="button" @click="on = !on" :class="on ? 'bg-emerald-600' : 'bg-gray-300'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                            <span class="sr-only">Toggle active</span>
                            <span :class="on ? 'translate-x-6' : 'translate-x-1'"
                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                        </button>
                        <span class="text-sm text-gray-700">Active</span>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-medium text-white
                               shadow-sm hover:bg-black/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-900/20">
                        Create
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
