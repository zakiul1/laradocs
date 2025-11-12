@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Customer</h1>
            <a href="{{ route('admin.customers.index') }}"
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                Back
            </a>
        </header>

        <form id="editCustomerForm" x-data="editCustomerForm()" x-on:submit.prevent="submit"
            action="{{ route('admin.customers.update', $customer) }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label>
                        <input name="name" type="text" required
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('name', $customer->name) }}">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input name="email" type="email"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('email', $customer->email) }}">
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone Number</label>
                        <input name="phone" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('phone', $customer->phone) }}">
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">WhatsApp Number</label>
                        <input name="whatsapp_number" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('whatsapp_number', $customer->whatsapp_number) }}">
                        @error('whatsapp_number')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Country</label>
                        <input name="country" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('country', $customer->country) }}">
                        @error('country')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Company Name</label>
                        <input name="company_name" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('company_name', $customer->company_name) }}">
                        @error('company_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Website</label>
                        <input name="website" type="url"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('website', $customer->website) }}">
                        @error('website')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Designation</label>
                        <input name="designation" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('designation', $customer->designation) }}">
                        @error('designation')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <textarea name="address" rows="4"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('address', $customer->address) }}</textarea>
                        @error('address')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Shipping Address</label>
                        <textarea name="shipping_address" rows="4"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('shipping_address', $customer->shipping_address) }}</textarea>
                        @error('shipping_address')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Current Photo + Dropzone --}}
            <div class="rounded-2xl p-6 bg-white/90 dark:bg-gray-800 shadow-lg" x-data="{ dragging: false, photoUrl: null }"
                @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                @drop.prevent="dragging=false; $refs.photo.files = $event.dataTransfer.files; $dispatch('change', { target: $refs.photo })">
                <label class="block text-sm font-medium mb-2">Photo</label>

                <div class="flex items-center gap-4 mb-3">
                    @if ($customer->photo_url)
                        <img src="{{ $customer->photo_url }}"
                            class="h-16 w-16 rounded-xl object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                            alt="">
                    @else
                        <div class="h-16 w-16 rounded-xl bg-gray-200 dark:bg-gray-700 grid place-items-center">👤</div>
                    @endif
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" name="remove_photo" value="1" class="rounded border-gray-300">
                        Remove current photo
                    </label>
                </div>

                <div class="border-2 border-dashed rounded-2xl p-6 text-center"
                    :class="dragging ? 'border-indigo-400 bg-indigo-50/40 dark:border-indigo-500/60 dark:bg-indigo-900/20' :
                        'border-gray-300 dark:border-gray-700'">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Drag & drop a new photo here, or
                        <button type="button" class="underline text-indigo-600"
                            @click="$refs.photo.click()">browse</button>
                    </p>
                    <input type="file" name="photo" accept="image/*" x-ref="photo" class="hidden"
                        @change="const f = $event.target.files?.[0]; if(!f){ photoUrl=null; return; } const r=new FileReader(); r.onload=()=>photoUrl=r.result; r.readAsDataURL(f);">
                    <template x-if="photoUrl">
                        <div class="mt-4 flex items-center justify-center gap-4">
                            <img :src="photoUrl"
                                class="h-24 w-24 rounded-xl object-cover ring-1 ring-gray-200 dark:ring-gray-700">
                            <button type="button" class="px-3 py-1.5 rounded-xl bg-gray-200 dark:bg-gray-700"
                                @click="$refs.photo.value=''; photoUrl=null;">
                                Remove
                            </button>
                        </div>
                    </template>
                </div>
                @error('photo')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Existing Documents + New --}}
            <div class="rounded-2xl p-6 bg-white/90 dark:bg-gray-800 shadow-lg" x-data="{ dragging: false, docs: [] }"
                @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                @drop.prevent="
                dragging=false;
                const incoming = Array.from($event.dataTransfer.files || []);
                const merged = [...docs, ...incoming];
                docs = merged;
                const dt = new DataTransfer(); merged.forEach(f => dt.items.add(f)); $refs.documents.files = dt.files;
             ">
                <label class="block text-sm font-medium mb-2">Documents</label>

                @if ($customer->documents)
                    <ul class="space-y-2 mb-3">
                        @foreach ($customer->documents as $path)
                            <li
                                class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2">
                                <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                    class="truncate hover:underline text-sm">{{ basename($path) }}</a>
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="remove_documents[]" value="{{ $path }}"
                                        class="rounded border-gray-300">
                                    Remove
                                </label>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="border-2 border-dashed rounded-2xl p-6 text-center"
                    :class="dragging ? 'border-indigo-400 bg-indigo-50/40 dark:border-indigo-500/60 dark:bg-indigo-900/20' :
                        'border-gray-300 dark:border-gray-700'">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Drag & drop new files here, or
                        <button type="button" class="underline text-indigo-600" @click="$refs.documents.click()">choose
                            files</button>
                    </p>
                    <input type="file" name="documents[]" multiple x-ref="documents" class="hidden"
                        @change="docs = [...docs, ...Array.from($event.target.files || [])];
                                const dt = new DataTransfer(); docs.forEach(f => dt.items.add(f)); $refs.documents.files = dt.files;">
                </div>

                <ul class="mt-3 space-y-2" x-show="docs.length" x-cloak>
                    <template x-for="(d, i) in docs" :key="i">
                        <li
                            class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2">
                            <span class="truncate text-sm" x-text="d.name"></span>
                            <button type="button" class="px-3 py-1.5 rounded-xl bg-gray-200 dark:bg-gray-700"
                                @click="docs.splice(i,1); const dt = new DataTransfer(); docs.forEach(f => dt.items.add(f)); $refs.documents.files = dt.files;">
                                Remove
                            </button>
                        </li>
                    </template>
                </ul>
                @error('documents.*')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Progress --}}
            <div x-data="{ p: 0, show: false }" x-init="window.addEventListener('customer-upload-progress', e => {
                show = true;
                p = e.detail;
            });
            window.addEventListener('customer-upload-hide', () => {
                show = false;
                p = 0;
            });" x-show="show" x-cloak
                class="rounded-2xl bg-white/90 dark:bg-gray-800 shadow-lg p-4">
                <div class="text-sm mb-2">Uploading... <span x-text="p + '%'"></span></div>
                <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                    <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-blue-600" :style="`width:${p}%;`">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90 disabled:opacity-60">
                    <span>Update</span>
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
            function editCustomerForm() {
                return {
                    submit() {
                        const form = document.getElementById('editCustomerForm');
                        const fd = new FormData(form);
                        fd.append('_method', 'PUT');

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', form.getAttribute('action'), true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        const token = form.querySelector('input[name=_token]')?.value;
                        if (token) xhr.setRequestHeader('X-CSRF-TOKEN', token);

                        xhr.upload.addEventListener('progress', (e) => {
                            if (e.lengthComputable) {
                                const pct = Math.round((e.loaded / e.total) * 100);
                                window.dispatchEvent(new CustomEvent('customer-upload-progress', {
                                    detail: pct
                                }));
                            }
                        });

                        xhr.onload = () => {
                            window.dispatchEvent(new Event('customer-upload-hide'));
                            if (xhr.status >= 200 && xhr.status < 300) {
                                let res = {};
                                try {
                                    res = JSON.parse(xhr.responseText || '{}');
                                } catch {}
                                window.safeToast?.('success', res.message ?? 'Customer updated');
                                window.location = @json(route('admin.customers.index'));
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
                            window.dispatchEvent(new Event('customer-upload-hide'));
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
