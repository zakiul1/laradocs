@extends('layouts.app')
@section('title', 'Categories — Siatex Docs')
@section('crumb', 'Categories')

@section('content')
    <x-section-title :title="'Categories (scope: ' . $scope . ')'" />

    @if (session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif
    @if ($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex items-center gap-3">
            <label class="text-sm">Scope</label>
            <select name="scope" class="rounded-lg border px-3 py-2">
                <option value="factory" {{ $scope === 'factory' ? 'selected' : '' }}>factory</option>
                <option value="employee" {{ $scope === 'employee' ? 'selected' : '' }}>employee</option>
                <!-- add more scopes as you add modules -->
            </select>
            <button class="px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 cursor-pointer">Filter</button>
        </form>
    </x-card>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Add root --}}
        <x-card>
            <form method="POST" action="{{ route('admin.categories.root.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="scope" value="{{ $scope }}">
                <div class="group">
                    <x-label value="Add root category (scope: {{ $scope }})" class="text-[0.95rem]" />
                    <x-input name="name" required class="rounded-xl border px-4 py-2.5" />
                </div>
                <button class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 cursor-pointer">Add
                    root</button>
            </form>
        </x-card>

        {{-- List + add child --}}
        <x-card class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="p-3">Root</th>
                        <th class="p-3">Children</th>
                        <th class="p-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($roots as $root)
                        <tr>
                            <td class="p-3 font-medium">{{ $root->name }}</td>
                            <td class="p-3">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($root->children as $child)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $child) }}"
                                            onsubmit="return confirm('Delete subcategory?')">
                                            @csrf @method('DELETE')
                                            <span
                                                class="inline-flex items-center gap-2 px-2 py-1 rounded-md border text-xs">
                                                {{ $child->name }}
                                                <button class="text-red-600 cursor-pointer" title="Delete">&times;</button>
                                            </span>
                                        </form>
                                    @empty
                                        <span class="text-xs text-gray-500">No subcategories</span>
                                    @endforelse
                                </div>

                                <form method="POST" action="{{ route('admin.categories.child.store') }}"
                                    class="mt-3 flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="scope" value="{{ $scope }}">
                                    <input type="hidden" name="parent_id" value="{{ $root->id }}">
                                    <x-input name="name" placeholder="New subcategory…"
                                        class="rounded-lg border px-3 py-1.5" />
                                    <button
                                        class="px-3 py-1.5 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">Add</button>
                                </form>
                            </td>
                            <td class="p-3">
                                <form method="POST" action="{{ route('admin.categories.destroy', $root) }}"
                                    onsubmit="return confirm('Delete root (and its children)?')">
                                    @csrf @method('DELETE')
                                    <button class="underline text-sm text-red-600 cursor-pointer">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-card>
    </div>
@endsection
