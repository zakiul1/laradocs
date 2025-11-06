@extends('layouts.app')
@section('title', 'Factories — Siatex Docs')
@section('crumb', 'Factories')

@section('content')
    <x-section-title :title="'Factories'">
        <x-slot:action>
            <a href="{{ route('admin.factories.create') }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                Add Factory
            </a>
        </x-slot:action>
    </x-section-title>

    @if (session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    <x-card class="overflow-x-auto -mx-4 md:mx-0">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="p-3">Name</th>
                    <th class="p-3">Categories</th>
                    <th class="p-3">Lines</th>
                    <th class="p-3">By</th>
                    <th class="p-3">Created</th>
                    <th class="p-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($factories as $f)
                    <tr>
                        <td class="p-3">
                            <div class="font-medium">{{ $f->name }}</div>
                            <div class="text-xs text-gray-500">{{ $f->address }}</div>
                            <div class="text-xs text-gray-500">{{ $f->phone }}</div>
                        </td>
                        <td class="p-3">
                            @forelse($f->categories as $cat)
                                <span
                                    class="inline-block text-xs px-2 py-1 rounded-md border mr-1 mb-1">{{ $cat->name }}</span>
                            @empty
                                <span class="text-xs text-gray-500">—</span>
                            @endforelse
                        </td>
                        <td class="p-3">{{ $f->lines }}</td>
                        <td class="p-3 text-xs">{{ $f->creator?->name }}</td>
                        <td class="p-3 text-xs">{{ $f->created_at->format('Y-m-d') }}</td>
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.factories.edit', $f) }}"
                                    class="underline text-sm cursor-pointer">Edit</a>
                                <form method="POST" action="{{ route('admin.factories.destroy', $f) }}"
                                    onsubmit="return confirm('Delete factory?')">
                                    @csrf @method('DELETE')
                                    <button class="underline text-sm text-red-600 cursor-pointer">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-6 text-center text-gray-500" colspan="6">No factories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $factories->links() }}</div>
    </x-card>
@endsection
