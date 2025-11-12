@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $customer->name }}</h1>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.index') }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Back
                </a>
                <a href="{{ route('admin.customers.edit', $customer) }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Edit
                </a>
                <form class="inline" method="POST" action="{{ route('admin.customers.destroy', $customer) }}"
                    onsubmit="return confirm('Delete this customer?')">
                    @csrf
                    @method('DELETE')
                    <button
                        class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                        Delete
                    </button>
                </form>
            </div>
        </header>

        <div class="rounded-2xl p-6 shadow-lg bg-white/90 dark:bg-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-4">
                    @if ($customer->photo_url)
                        <img class="rounded-2xl w-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                            src="{{ $customer->photo_url }}" alt="Photo of {{ $customer->name }}">
                    @else
                        <div
                            class="aspect-square rounded-2xl bg-gray-200 dark:bg-gray-700 grid place-items-center text-4xl">
                            👤</div>
                    @endif

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Created</p>
                            <p class="font-medium">{{ $customer->created_at?->format('Y-m-d H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Updated</p>
                            <p class="font-medium">{{ $customer->updated_at?->format('Y-m-d H:i') }}</p>
                        </div>
                        @if ($customer->creator)
                            <div class="md:col-span-2">
                                <p class="text-gray-500 dark:text-gray-400">Created By</p>
                                <p class="font-medium">{{ $customer->creator->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-1">Email</label>
                            <p
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                {{ $customer->email ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone</label>
                            <p
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                {{ $customer->phone ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">WhatsApp</label>
                            <p
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                {{ $customer->whatsapp_number ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Country</label>
                            <p
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                {{ $customer->country ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Company</label>
                            <p
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                {{ $customer->company_name ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Website</label>
                            <p
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                {{ $customer->website ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Designation</label>
                            <p
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                {{ $customer->designation ?? '—' }}
                            </p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Address</label>
                            <div
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-white/70 dark:bg-gray-900/40 min-h-[64px]">
                                {!! nl2br(e($customer->address ?? '—')) !!}
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Shipping Address</label>
                            <div
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-white/70 dark:bg-gray-900/40 min-h-[64px]">
                                {!! nl2br(e($customer->shipping_address ?? '—')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium mb-2">Documents</label>
                        @if ($customer->documents && count($customer->documents))
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach ($customer->documents as $path)
                                    <li
                                        class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2">
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                            class="truncate hover:underline text-sm">
                                            {{ basename($path) }}
                                        </a>
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                            class="text-xs px-3 py-1 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                                            View
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">No documents uploaded.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
