@extends('layouts.app')
@section('title', 'Employees — Siatex Docs')
@section('crumb', 'Employees')

@section('content')
    <x-section-title :title="'Employees'" :subtitle="'Manage employee records'">
        <x-slot name="action">
            @include('admin.employees.partials.create-btn')
        </x-slot>
    </x-section-title>

    <x-card class="overflow-x-auto -mx-4 md:mx-0">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-3">Name</th>
                    <th class="p-3">Designation</th> {{-- NEW --}}
                    <th class="p-3">Phone</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Join</th>
                    <th class="p-3">Leave</th>
                    <th class="p-3">Docs</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($employees as $e)
                    @php
                        $docs = $e->documents; // eager-load in controller if possible
                    @endphp
                    <tr>
                        <td class="p-3 flex items-center gap-3">
                            <img src="{{ $e->photoUrl() ?: 'https://placehold.co/40x40?text=E' }}"
                                class="w-10 h-10 rounded-full object-cover" alt="">
                            <div>
                                <div class="font-medium">{{ $e->name }}</div>
                                <div class="text-xs text-gray-500">{{ $e->address }}</div>
                            </div>
                        </td>

                        {{-- NEW: Designation column --}}
                        <td class="p-3">
                            {{ $e->designation ?: '—' }}
                        </td>

                        <td class="p-3">{{ $e->phone }}</td>
                        <td class="p-3">{{ $e->email }}</td>
                        <td class="p-3">{{ optional($e->join_date)->format('Y-m-d') }}</td>
                        <td class="p-3">{{ optional($e->leave_date)->format('Y-m-d') }}</td>

                        {{-- Docs column with download buttons --}}
                        <td class="p-3">
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button type="button" @click="open = !open"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 5v10M7 10l5 5 5-5" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span>Docs ({{ $docs->count() }})</span>
                                </button>

                                {{-- Dropdown list of document download links --}}
                                <div x-cloak x-show="open" @click.outside="open=false"
                                    class="absolute z-10 mt-2 w-64 rounded-lg border bg-white shadow">
                                    @if ($docs->isEmpty())
                                        <div class="px-3 py-2 text-sm text-gray-500">No documents</div>
                                    @else
                                        <ul class="max-h-64 overflow-auto">
                                            @foreach ($docs as $doc)
                                                <li class="border-b last:border-0">
                                                    <a href="{{ asset('storage/' . $doc->path) }}"
                                                        download="{{ $doc->original_name }}"
                                                        class="flex items-center justify-between gap-2 px-3 py-2 hover:bg-gray-50">
                                                        <span class="truncate text-sm">{{ $doc->original_name }}</span>
                                                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none">
                                                            <path d="M12 5v10m0 0l-4-4m4 4l4-4M5 19h14"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="p-3 flex gap-2">
                            <a class="underline text-sm" href="{{ route('admin.employees.edit', $e) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.employees.destroy', $e) }}"
                                onsubmit="return confirm('Delete employee?')">
                                @csrf @method('DELETE')
                                <button class="underline text-sm text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-6 text-center text-gray-500" colspan="8">No employees yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $employees->links() }}</div>
    </x-card>
@endsection
