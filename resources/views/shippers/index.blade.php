@extends('layouts.app')

@section('content')
    <div x-data>
        <div class="max-w-7xl mx-auto p-6 space-y-6">
            <header class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Shippers</h1>
                <a href="{{ route('admin.shippers.create') }}"
                    class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                    + Add Shipper
                </a>
            </header>

            @php
                $q = request('q');
                $sort = request('sort', 'created_at');
                $dir = request('dir', 'desc');
                $nextDir = $dir === 'asc' ? 'desc' : 'asc';
            @endphp

            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Search</label>
                    <input name="q" value="{{ $q }}" placeholder="Search name / email / phone / website"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                        x-on:keydown.window.prevent.slash="$el.focus()" />
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button
                        class="w-full px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                        Filter
                    </button>
                </div>
            </form>

            <div class="overflow-x-auto rounded-2xl shadow-lg bg-white/90 dark:bg-gray-800">
                <table class="min-w-full">
                    <thead class="text-left text-sm text-gray-600 dark:text-gray-300">
                        @php
                            $columns = [
                                'name' => 'Name',
                                'email' => 'Email',
                                'phone' => 'Phone',
                                'website' => 'Website',
                                'created_at' => 'Created',
                            ];
                        @endphp
                        <tr>
                            @foreach ($columns as $key => $label)
                                <th class="px-4 py-3">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'dir' => $sort === $key ? $nextDir : 'asc']) }}"
                                        class="hover:underline">
                                        {{ $label }}
                                        @if ($sort === $key)
                                            <span>{{ $dir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($shippers as $s)
                            <tr class="text-sm">
                                <td class="px-4 py-3">
                                    <a class="font-semibold hover:underline"
                                        href="{{ route('admin.shippers.show', $s) }}">{{ $s->name }}</a>
                                </td>
                                <td class="px-4 py-3">{{ $s->email ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $s->phone ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($s->website)
                                        <a href="{{ $s->website }}" target="_blank"
                                            class="text-indigo-600 hover:underline">{{ $s->website }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $s->created_at?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.shippers.edit', $s) }}"
                                        class="px-3 py-1.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                                        Edit
                                    </a>
                                    <button
                                        class="px-3 py-1.5 rounded-xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                                        x-data
                                        @click="
                                        $dispatch('open-delete', {
                                            url: '{{ route('admin.shippers.destroy', $s) }}',
                                            title: 'Delete Shipper',
                                            message: 'Are you sure you want to delete this shipper?'
                                        })
                                    ">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">No shippers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $shippers->links() }}
            </div>
        </div>

        {{-- Inline delete modal --}}
        <div x-data="{ open: false, url: '', title: 'Delete Confirmation', message: 'Are you sure you want to delete this item?' }"
            x-on:open-delete.window="open = true; url = $event.detail.url; title = $event.detail.title ?? title; message = $event.detail.message ?? message;"
            x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/40" x-on:click="open=false"></div>

            <div class="relative w-full max-w-md rounded-2xl p-6 shadow-lg bg-white/90 dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100" x-text="title"></h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300" x-text="message"></p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                        x-on:click="open=false" type="button">Cancel</button>

                    <form method="POST" :action="url" x-ref="form">
                        @csrf
                        @method('DELETE')
                        <button
                            class="px-4 py-2 rounded-xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                            type="submit">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
