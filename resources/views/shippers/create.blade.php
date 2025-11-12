@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Add Shipper</h1>
            <a href="{{ route('admin.shippers.index') }}"
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">Back</a>
        </header>

        <form id="shipperCreateForm" x-data="shipperCreate()" x-on:submit.prevent="submit"
            action="{{ route('admin.shippers.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label>
                        <input name="name" required type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('name') }}">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input name="email" type="email"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('email') }}">
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input name="phone" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('phone') }}">
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Website</label>
                        <input name="website" type="url"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('website') }}">
                        @error('website')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4 md:col-span-1">
                    <div>
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <textarea name="address" rows="6"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                    <span>Create</span>
                </button>
            </div>

            @if ($errors->any())
                <div class="rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-200 p-4">
                    <ul class="list-disc pl-6">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>

    @push('scripts')
        <script>
            function shipperCreate() {
                return {
                    submit() {
                        const form = document.getElementById('shipperCreateForm');
                        const fd = new FormData(form);

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', form.getAttribute('action'), true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        const token = form.querySelector('input[name=_token]')?.value;
                        if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);

                        xhr.onload = () => {
                            if (xhr.status >= 200 && xhr.status < 300) {
                                let res = {};
                                try {
                                    res = JSON.parse(xhr.responseText || '{}');
                                } catch {}
                                window.safeToast?.('success', res.message ?? 'Shipper created');
                                window.location = @json(route('admin.shippers.index'));
                                return;
                            }
                            if (xhr.status === 422) {
                                try {
                                    const json = JSON.parse(xhr.responseText);
                                    const firstMsg = json?.message || Object.values(json?.errors || {})?.[0]?.[0] ||
                                        'Validation error';
                                    window.safeToast?.('error', firstMsg);
                                } catch {
                                    window.safeToast?.('error', 'Validation error');
                                }
                            } else {
                                window.safeToast?.('error', 'Request failed. Submitting normally...');
                            }
                            form.removeAttribute('x-on:submit');
                            form.submit();
                        };

                        xhr.onerror = () => {
                            window.safeToast?.('error', 'Network error. Submitting normally...');
                            form.removeAttribute('x-on:submit');
                            form.submit();
                        };

                        xhr.send(fd);
                    }
                }
            }
        </script>
    @endpush
@endsection
