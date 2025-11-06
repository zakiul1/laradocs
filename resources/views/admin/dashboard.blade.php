@extends('layouts.app')

@section('title', 'Dashboard — Siatex Docs')
@section('crumb', 'Dashboard')

@section('content')
    <x-section-title title="Dashboard" subtitle="Overview of your application">
        @if (auth()->user()->isSuperAdmin())
            <x-slot name="action">
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center px-4 py-2 rounded-xl border bg-gray-900 text-white text-sm hover:bg-gray-800 transition">
                    Register Admin
                </a>
            </x-slot>
        @endif
    </x-section-title>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card>
            <div class="text-sm text-gray-500">Total Users</div>
            <div class="mt-2 text-2xl font-semibold">{{ \App\Models\User::count() }}</div>
        </x-card>

        <x-card>
            <div class="text-sm text-gray-500">Active Users</div>
            <div class="mt-2 text-2xl font-semibold">{{ \App\Models\User::where('is_active', true)->count() }}</div>
        </x-card>

        <x-card>
            <div class="text-sm text-gray-500">Admins</div>
            <div class="mt-2 text-2xl font-semibold">
                {{ \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->count() }}</div>
        </x-card>

        <x-card>
            <div class="text-sm text-gray-500">Super Admins</div>
            <div class="mt-2 text-2xl font-semibold">
                {{ \App\Models\User::where('role', \App\Models\User::ROLE_SUPER_ADMIN)->count() }}</div>
        </x-card>
    </div>

    {{-- Recent users --}}
    <div class="mt-6">
        <x-card>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Recent Users</h2>
                <a class="text-sm underline" href="{{ route('admin.users.index') }}">View all</a>
            </div>
            <div class="mt-4 overflow-x-auto -mx-4 md:mx-0">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="p-3">Name</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach (\App\Models\User::orderByDesc('created_at')->limit(6)->get() as $u)
                            <tr>
                                <td class="p-3">{{ $u->name }}</td>
                                <td class="p-3">{{ $u->email }}</td>
                                <td class="p-3">{{ $u->role }}</td>
                                <td class="p-3">
                                    @if ($u->is_active)
                                        <span
                                            class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Inactive</span>
                                    @endif
                                </td>
                                <td class="p-3">{{ $u->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
@endsection
