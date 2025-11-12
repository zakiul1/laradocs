@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">Factory Subcategories</h1>

        {{-- Alerts --}}
        @if (session('success'))
            <div
                class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div
                class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/40 dark:text-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Add Subcategory --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <h5 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">Add New Subcategory</h5>
                <form method="post" action="{{ route('admin.factory-subcategories.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Parent
                            Category</label>
                        <select name="factory_category_id" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                            <option value="">— Select —</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input name="name" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Position</label>
                        <input type="number" name="position" value="0" min="0"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
                    </div>

                    <button
                        class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-auto">
                        Add Subcategory
                    </button>
                </form>
            </div>

            {{-- Subcategories Table --}}
            <div class="lg:col-span-2">
                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                        Subcategory
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                        Category
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                        Position
                                    </th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">
                                        Updated
                                    </th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse($subcategories as $sub)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
                                            {{ $sub->name }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                            {{ $sub->category?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                            {{ $sub->position }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                            {{ $sub->updated_at->diffForHumans() }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex flex-wrap items-center justify-end gap-2">
                                                {{-- Inline update --}}
                                                <form method="post"
                                                    action="{{ route('admin.factory-subcategories.update', $sub) }}"
                                                    class="flex flex-wrap items-center gap-2">
                                                    @csrf @method('PUT')

                                                    <select name="factory_category_id"
                                                        class="w-40 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                                                        @foreach ($categories as $c)
                                                            <option value="{{ $c->id }}"
                                                                @selected($sub->factory_category_id == $c->id)>
                                                                {{ $c->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <input type="text" name="name" value="{{ $sub->name }}"
                                                        class="w-40 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />

                                                    <input type="number" name="position" value="{{ $sub->position }}"
                                                        class="w-20 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />

                                                    <button
                                                        class="inline-flex items-center rounded-lg border border-blue-500 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                                        Save
                                                    </button>
                                                </form>

                                                {{-- Delete --}}
                                                <form method="post"
                                                    action="{{ route('admin.factory-subcategories.destroy', $sub) }}"
                                                    onsubmit="return confirm('Delete subcategory?')" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button
                                                        class="inline-flex items-center rounded-lg border border-red-500 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-400 dark:text-red-400 dark:hover:bg-red-900/30">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No subcategories yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                        {{ $subcategories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
