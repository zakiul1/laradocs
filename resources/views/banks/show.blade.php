@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $bank->name }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.banks.index') }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Back
                </a>
                <a href="{{ route('admin.banks.edit', $bank) }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Edit
                </a>
                <form class="inline" method="POST" action="{{ route('admin.banks.destroy', $bank) }}"
                    onsubmit="return confirm('Delete this bank?')">
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <label class="block text-sm font-medium mb-1">Type</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->type }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->email ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->phone ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Country</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->country ?? '—' }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Address</label>
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-white/70 dark:bg-gray-900/40 min-h-[64px]">
                        {!! nl2br(e($bank->address ?? '—')) !!}
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Note</label>
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-white/70 dark:bg-gray-900/40 min-h-[64px]">
                        {!! nl2br(e($bank->note ?? '—')) !!}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 md:col-span-2">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Created</p>
                        <p class="font-medium">{{ $bank->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Updated</p>
                        <p class="font-medium">{{ $bank->updated_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    @if ($bank->creator)
                        <div class="md:col-span-2">
                            <p class="text-gray-500 dark:text-gray-400">Created By</p>
                            <p class="font-medium">{{ $bank->creator->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
