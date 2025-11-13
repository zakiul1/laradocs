@extends('layouts.app')

@section('content')
    <div x-data>
        <div class="max-w-7xl mx-auto p-6 space-y-6">
            <header class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Companies</h1>
                <a href="{{ route('admin.companies.create') }}"
                    class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                    + Add Company
                </a>
            </header>

            @php
                $q = request('q');
                $category = request('category');
                $sort = request('sort', 'created_at');
                $dir = request('dir', 'desc');
                $nextDir = $dir === 'asc' ? 'desc' : 'asc';
            @endphp

            {{-- Filters --}}
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium mb-1">Search</label>
                    <input name="q" value="{{ $q }}"
                        placeholder="Search company name / email / contact / phone / website"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                        x-on:keydown.window.prevent.slash="$el.focus()" />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Category</label>
                    <select name="category"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                        <option value="">All</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" @selected($category == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-5 flex gap-3">
                    <button
                        class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                        Filter
                    </button>
                    <a href="{{ route('admin.companies.index') }}"
                        class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                        Reset
                    </a>
                </div>
            </form>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-2xl shadow-lg bg-white/90 dark:bg-gray-800">
                <table class="min-w-full">
                    <thead class="text-left text-sm text-gray-600 dark:text-gray-300">
                        @php
                            $columns = [
                                'company_name' => 'Company',
                                'email' => 'Email',
                                'phone' => 'Phone',
                                'contact' => 'Contact',
                                'category' => 'Category',
                                'created_at' => 'Created',
                            ];
                        @endphp
                        <tr>
                            <th class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'company_name', 'dir' => $sort === 'company_name' ? $nextDir : 'asc']) }}"
                                    class="hover:underline flex items-center gap-1">
                                    <span>Company</span>
                                    @if ($sort === 'company_name')
                                        <span>{{ $dir === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Phone</th>
                            <th class="px-4 py-3">Contact</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'dir' => $sort === 'created_at' ? $nextDir : 'asc']) }}"
                                    class="hover:underline flex items-center gap-1">
                                    <span>Created</span>
                                    @if ($sort === 'created_at')
                                        <span>{{ $dir === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($companies as $c)
                            @php
                                $catName = $c->category?->name;
                            @endphp
                            <tr class="text-sm">
                                <td class="px-4 py-3">
                                    <a class="font-semibold hover:underline" href="{{ route('admin.companies.show', $c) }}">
                                        {{ $c->company_name }}
                                    </a>
                                    @if ($c->name)
                                        <div class="text-xs text-gray-500">{{ $c->name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $c->email ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $c->phone ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $c->contact_person ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    {{ $catName ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $c->created_at?->format('Y-m-d') }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.companies.edit', $c) }}"
                                        class="px-3 py-1.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                                        Edit
                                    </a>
                                    <button
                                        class="px-3 py-1.5 rounded-xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                                        x-data
                                        @click="
                                        $dispatch('open-delete', {
                                            url: '{{ route('admin.companies.destroy', $c) }}',
                                            title: 'Delete Company',
                                            message: 'Are you sure you want to delete this company?'
                                        })
                                    ">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    No companies found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $companies->links() }}
            </div>
        </div>

        {{-- Delete modal (same pattern as banks) --}}
        <div x-data="{ open: false, url: '', title: 'Delete Confirmation', message: 'Are you sure?' }"
            x-on:open-delete.window="open = true; url = $event.detail.url; title = $event.detail.title ?? title; message = $event.detail.message ?? message;"
            x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" x-on:click="open=false"></div>

            <div class="relative w-full max-w-md rounded-2xl p-6 shadow-lg bg-white/90 dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="title"></h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300" x-text="message"></p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                        x-on:click="open=false" type="button">
                        Cancel
                    </button>

                    <form method="POST" :action="url" x-ref="form">
                        @csrf
                        @method('DELETE')
                        <button
                            class="px-4 py-2 rounded-xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                            type="submit">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
