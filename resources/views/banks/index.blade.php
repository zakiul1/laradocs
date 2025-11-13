@extends('layouts.app')

@section('content')
    <div x-data>
        <div class="max-w-7xl mx-auto p-6 space-y-6">
            <header class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Banks</h1>
                <a href="{{ route('admin.banks.create') }}"
                    class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                    + Add Bank
                </a>
            </header>

            @php
                use App\Models\Customer;
                use App\Models\Company;

                $q = request('q');
                $type = request('type');
                $country = request('country');
                $sort = request('sort', 'created_at');
                $dir = request('dir', 'desc');
                $nextDir = $dir === 'asc' ? 'desc' : 'asc';
            @endphp

            {{-- Filters --}}
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Search</label>
                    <input name="q" value="{{ $q }}" placeholder="Search name / phone / note / company"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                        x-on:keydown.window.prevent.slash="$el.focus()" />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Type</label>
                    <select name="type"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                        <option value="">All</option>
                        @foreach (['Customer Bank', 'Shipper Bank'] as $t)
                            <option value="{{ $t }}" @selected($type === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Country</label>
                    <input name="country" value="{{ $country }}"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2" />
                </div>

                <div class="md:col-span-5 flex gap-3">
                    <button
                        class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                        Filter
                    </button>
                    <a href="{{ route('admin.banks.index') }}"
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
                                'name' => 'Name',
                                'type' => 'Type',
                                'company' => 'Company',
                                'phone' => 'Phone',
                                'country' => 'Country',
                                'created_at' => 'Created',
                            ];
                        @endphp
                        <tr>
                            @foreach ($columns as $key => $label)
                                <th class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => $key === 'company' ? 'company_name' : $key, 'dir' => $sort === ($key === 'company' ? 'company_name' : $key) ? $nextDir : 'asc']) }}"
                                        class="hover:underline flex items-center gap-1">
                                        <span>{{ $label }}</span>
                                        @if ($sort === $key || ($key === 'company' && $sort === 'company_name'))
                                            <span>{{ $dir === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </a>
                                </th>
                            @endforeach
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($banks as $b)
                            @php
                                $typeBadgeClass = match ($b->type) {
                                    'Customer Bank'
                                        => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
                                    'Shipper Bank'
                                        => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                };

                                $relatedCompany = $b->company;

                                $companyTypeKey = null;
                                $companyTypeLabel = null;
                                $companyDisplayName = null;

                                if ($b->company_type === Customer::class) {
                                    $companyTypeKey = 'customer';
                                    $companyTypeLabel = 'Customer';
                                    $companyDisplayName = $relatedCompany->name ?? ($b->company_name ?? null);
                                } elseif ($b->company_type === Company::class) {
                                    $companyTypeKey = 'shipper';
                                    $companyTypeLabel = 'Shipper';
                                    $companyDisplayName =
                                        $relatedCompany->company_name ??
                                        ($relatedCompany->name ?? ($b->company_name ?? null));
                                }

                                $companyBadgeClass =
                                    $companyTypeKey === 'customer'
                                        ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200'
                                        : ($companyTypeKey === 'shipper'
                                            ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200'
                                            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200');
                            @endphp

                            <tr class="text-sm">
                                {{-- Name --}}
                                <td class="px-4 py-3">
                                    <a class="font-semibold hover:underline" href="{{ route('admin.banks.show', $b) }}">
                                        {{ $b->name }}
                                    </a>
                                </td>

                                {{-- Type --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-xl text-xs {{ $typeBadgeClass }}">
                                        {{ $b->type ?? '—' }}
                                    </span>
                                </td>

                                {{-- Company --}}
                                <td class="px-4 py-3">
                                    @if ($companyDisplayName)
                                        <div class="flex flex-col gap-0.5">
                                            <span class="font-medium text-gray-800 dark:text-gray-100">
                                                {{ $companyDisplayName }}
                                            </span>
                                            @if ($companyTypeLabel)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-xl text-[11px] {{ $companyBadgeClass }}">
                                                    {{ $companyTypeLabel }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Phone --}}
                                <td class="px-4 py-3">
                                    {{ $b->phone ?? '—' }}
                                </td>

                                {{-- Country --}}
                                <td class="px-4 py-3">
                                    {{ $b->country ?? '—' }}
                                </td>

                                {{-- Created --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ $b->created_at?->format('Y-m-d') }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.banks.edit', $b) }}"
                                        class="px-3 py-1.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                                        Edit
                                    </a>
                                    <button
                                        class="px-3 py-1.5 rounded-xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                                        x-data
                                        @click="
                                            $dispatch('open-delete', {
                                                url: '{{ route('admin.banks.destroy', $b) }}',
                                                title: 'Delete Bank',
                                                message: 'Are you sure you want to delete this bank?'
                                            })
                                        ">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    No banks found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $banks->links() }}
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
