@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Factories</h1>
            <a href="{{ route('admin.factories.create') }}"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                + Add New
            </a>
        </div>

        {{-- Filters --}}
        <form method="get"
            class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                {{-- Category --}}
                <div>
                    <label for="filter_category" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Category
                    </label>
                    <select id="filter_category" name="category_id"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        <option value="">— Any —</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" @selected(request('category_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Subcategory --}}
                <div>
                    <label for="filter_subcategory" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Subcategory
                    </label>
                    <select id="filter_subcategory" name="subcategory_id"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        <option value="">— Any —</option>
                        @foreach ($subcategories as $id => $name)
                            <option value="{{ $id }}" @selected(request('subcategory_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="flex items-end gap-3">
                    <button
                        class="inline-flex w-full items-center justify-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 sm:w-auto">
                        Filter
                    </button>
                    <a href="{{ route('admin.factories.index') }}"
                        class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300 sm:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- Factories Table --}}
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">#</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Name</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Category</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Subcategory</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Employees</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Lines</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">Phone</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($factories as $f)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $f->id }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $f->name }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $f->category?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $f->subcategory?->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $f->total_employees }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $f->lines }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $f->phone ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.factories.edit', $f) }}"
                                            class="inline-flex items-center rounded-lg border border-blue-500 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 dark:border-blue-400 dark:text-blue-400 dark:hover:bg-blue-900/30">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.factories.destroy', $f) }}" method="post"
                                            onsubmit="return confirm('Delete factory?')" class="inline">
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
                                <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No factories found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                {{ $factories->links() }}
            </div>
        </div>
    </div>

    {{-- JS: Dynamic subcategory load --}}
    <script>
        document.getElementById('filter_category')?.addEventListener('change', function() {
            const catId = this.value;
            const subSel = document.getElementById('filter_subcategory');
            subSel.innerHTML = '<option value="">Loading…</option>';
            fetch(`{{ route('admin.factories.subcategories.json') }}?category_id=${catId}`)
                .then(r => r.json())
                .then(rows => {
                    subSel.innerHTML = '<option value="">— Any —</option>';
                    rows.forEach(r => {
                        const opt = document.createElement('option');
                        opt.value = r.id;
                        opt.textContent = r.name;
                        subSel.appendChild(opt);
                    });
                });
        });
    </script>
@endsection
