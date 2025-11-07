@extends('layouts.app')
@section('title', 'Categories — Siatex Docs')
@section('crumb', 'Categories')

@section('content')
    <x-section-title :title="'Categories'" :subtitle="'Create, organize, and manage categories per module (scope) like WordPress'">
        <x-slot name="action">
            <form method="GET" class="flex items-center gap-2">
                <select name="scope" class="rounded-md border px-3 py-2">
                    @foreach ($scopes as $s)
                        <option value="{{ $s }}" @selected($s === $scope)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 rounded-md border bg-white hover:bg-gray-50">Switch</button>
            </form>
        </x-slot>
    </x-section-title>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Left: Add New Category (like WordPress) --}}
        <x-card>
            <h3 class="text-base font-semibold mb-3">Add New {{ ucfirst($scope) }} Category</h3>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="scope" value="{{ $scope }}" />

                <div>
                    <x-label value="Name" />
                    <x-input name="name" required />
                </div>

                <div>
                    <x-label value="Parent Category" />
                    <select name="parent_id" class="w-full rounded-md border px-3 py-2">
                        <option value="">None</option>
                        @foreach ($allForSelect as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Choose a parent to create a subcategory.</p>
                </div>

                <div class="pt-2">
                    <button class="px-4 py-2 rounded-md bg-gray-900 text-white">Add New Category</button>
                </div>
            </form>
        </x-card>

        {{-- Right: Hierarchical list with inline Edit/Delete (like WordPress) --}}
        <div class="md:col-span-2">
            <x-card>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="p-3">Name</th>
                                <th class="p-3">Parent</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($roots as $root)
                                @include('admin.categories.partials._row', ['cat' => $root, 'depth' => 0])
                            @empty
                                <tr>
                                    <td colspan="3" class="p-6 text-center text-gray-500">No categories yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
@endsection
