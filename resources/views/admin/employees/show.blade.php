{{-- resources/views/employees/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div x-data x-init="(function ensureAlpine() {
        if (window.Alpine) { initToast(); return; }
        const s = document.createElement('script');
        s.defer = true;
        s.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
        s.onload = () => initToast();
        document.head.appendChild(s);
    })();
    
    function initToast() {
        document.addEventListener('alpine:init', () => {
            Alpine.store('toast', {
                show: false,
                type: 'success',
                message: '',
                trigger(type, message) {
                    this.type = type;
                    this.message = message;
                    this.show = true;
                    setTimeout(() => this.show = false, 3000);
                }
            });
            @if(session('success'))
            setTimeout(() => Alpine.store('toast').trigger('success', @json(session('success'))), 0);
            @endif
            @if(session('error'))
            setTimeout(() => Alpine.store('toast').trigger('error', @json(session('error'))), 0);
            @endif
        });
    }">
        <div class="max-w-6xl mx-auto p-6 space-y-6">
            <header class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold">{{ $employee->name }}</h1>
                    @php
                        $badge = match ($employee->status) {
                            'Active' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
                            'Inactive' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
                            'Resigned' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                        };
                    @endphp
                    <span class="px-2 py-1 rounded-xl text-xs {{ $badge }}">{{ $employee->status }}</span>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.employees.index') }}"
                        class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                        Back
                    </a>
                    <a href="{{ route('admin.employees.edit', $employee) }}"
                        class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                        Edit
                    </a>
                    <button
                        class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                        x-data
                        @click="
                        $dispatch('open-delete', {
                            url: '{{ route('admin.employees.destroy', $employee) }}',
                            title: 'Delete Employee',
                            message: 'Delete this employee? You can choose permanent delete in the modal.'
                        })
                    ">
                        Delete
                    </button>
                </div>
            </header>

            {{-- Toast --}}
            <div x-data x-show="$store.toast && $store.toast.show" x-transition
                class="fixed top-4 right-4 z-50 rounded-xl px-4 py-3 shadow-lg"
                :class="{
                    'bg-green-600 text-white': $store.toast?.type === 'success',
                    'bg-red-600 text-white': $store.toast?.type === 'error',
                    'bg-blue-600 text-white': $store.toast?.type === 'info'
                }"
                x-text="$store.toast?.message" x-cloak></div>

            {{-- Profile Card --}}
            <div class="rounded-2xl p-6 shadow-lg bg-white/90 dark:bg-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        @if ($employee->photo_url)
                            <img class="rounded-2xl w-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                                src="{{ $employee->photo_url }}" alt="Photo of {{ $employee->name }}">
                        @else
                            <div
                                class="aspect-square rounded-2xl bg-gray-200 dark:bg-gray-700 grid place-items-center text-4xl">
                                👤</div>
                        @endif

                        {{-- Quick facts --}}
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Joined</p>
                                <p class="font-medium">{{ optional($employee->join_date)->format('Y-m-d') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Left</p>
                                <p class="font-medium">{{ optional($employee->leave_date)->format('Y-m-d') ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Gender</p>
                                <p class="font-medium">{{ $employee->gender ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Blood Group</p>
                                <p class="font-medium">{{ $employee->blood_group ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium mb-1">Phone</label>
                                <p
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                    {{ $employee->phone }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Email</label>
                                <p
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                    {{ $employee->email ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Designation</label>
                                <p
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                    {{ $employee->designation ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Alternative Contact</label>
                                <p
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                    {{ $employee->alternative_contact_number ?? '—' }}
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1">Address</label>
                                <p
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                    {{ $employee->address ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Emergency Contact</label>
                                <p
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                    {{ $employee->emergency_contact_name ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Emergency Phone / Relation</label>
                                <p
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                                    {{ $employee->emergency_contact_phone ?? '—' }}{{ $employee->emergency_contact_relation ? ' — ' . $employee->emergency_contact_relation : '' }}
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1">Notes</label>
                                <div
                                    class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-white/70 dark:bg-gray-900/40 min-h-[64px]">
                                    {!! nl2br(e($employee->notes ?? '—')) !!}
                                </div>
                            </div>
                        </div>

                        {{-- Documents --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium mb-2">Documents</label>
                            @if ($employee->documents && count($employee->documents))
                                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach ($employee->documents as $path)
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

            {{-- Optional: Secondary actions card --}}
            <div class="rounded-2xl p-6 shadow-lg bg-white/90 dark:bg-gray-800">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div class="text-sm text-gray-600 dark:text-gray-300">
                        Created: {{ $employee->created_at->format('Y-m-d H:i') }}
                        @if ($employee->updated_at && !$employee->updated_at->equalTo($employee->created_at))
                            • Updated: {{ $employee->updated_at->format('Y-m-d H:i') }}
                        @endif
                        @if ($employee->creator)
                            • By: {{ $employee->creator->name }}
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.employees.edit', $employee) }}"
                            class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                            Edit
                        </a>
                        <button
                            class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                            x-data
                            @click="
                            $dispatch('open-delete', {
                                url: '{{ route('admin.employees.destroy', $employee) }}',
                                title: 'Delete Employee',
                                message: 'Delete this employee? You can choose permanent delete in the modal.'
                            })
                        ">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Global delete confirmation modal --}}
        @include('components.delete-modal')
    </div>
@endsection
