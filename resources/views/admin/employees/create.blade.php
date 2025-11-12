@extends('layouts.app')

@section('content')
    <div x-data x-init="// Load Alpine if it's not present (safe for layouts that already include Alpine)
    (function ensureAlpine() {
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
            // Flash success from session (if any)
            @if(session('success'))
            setTimeout(() => Alpine.store('toast').trigger('success', @json(session('success'))), 0);
            @endif
        });
    }">
        <div class="max-w-6xl mx-auto p-6 space-y-6">
            <header class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Add Employee</h1>
                <a href="{{ route('admin.employees.index') }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Back
                </a>
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

            <form id="employeeForm" x-data="employeeForm()" x-on:submit.prevent="submit" class="space-y-6"
                action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Grid fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Column 1 --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label>
                            <input name="name" type="text" required
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('name') }}">
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Phone <span class="text-red-500">*</span></label>
                            <input name="phone" type="text" required
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('phone') }}">
                            @error('phone')
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

                        <div>
                            <label class="block text-sm font-medium mb-1">Designation</label>
                            <input name="designation" type="text"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('designation') }}">
                            @error('designation')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Join Date <span
                                    class="text-red-500">*</span></label>
                            <input name="join_date" type="date" required
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('join_date') }}">
                            @error('join_date')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Leave Date</label>
                            <input name="leave_date" type="date"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('leave_date') }}">
                            @error('leave_date')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Column 2 --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Gender</label>
                            <select name="gender"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                                <option value="">Select</option>
                                @foreach (['Male', 'Female', 'Other'] as $g)
                                    <option value="{{ $g }}" @selected(old('gender') === $g)>{{ $g }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gender')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Blood Group</label>
                            <input name="blood_group" type="text"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('blood_group') }}">
                            @error('blood_group')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Alternative Contact Number</label>
                            <input name="alternative_contact_number" type="text"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('alternative_contact_number') }}">
                            @error('alternative_contact_number')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Emergency Contact Name</label>
                            <input name="emergency_contact_name" type="text"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('emergency_contact_name') }}">
                            @error('emergency_contact_name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Emergency Contact Phone</label>
                            <input name="emergency_contact_phone" type="text"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('emergency_contact_phone') }}">
                            @error('emergency_contact_phone')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Emergency Contact Relation</label>
                            <input name="emergency_contact_relation" type="text"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                value="{{ old('emergency_contact_relation') }}">
                            @error('emergency_contact_relation')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Column 3 --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                                @foreach (['Active', 'Inactive', 'Resigned'] as $s)
                                    <option value="{{ $s }}" @selected(old('status', $s) === $s)>{{ $s }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Address</label>
                            <textarea name="address" rows="5"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Notes</label>
                            <textarea name="notes" rows="5"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Photo: dropzone + preview --}}
                <div class="rounded-2xl p-6 bg-white/90 dark:bg-gray-800 shadow-lg" x-data="{ dragging: false }"
                    @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                    @drop.prevent="dragging=false; $refs.photo.files = $event.dataTransfer.files; $dispatch('change', { target: $refs.photo })">
                    <label class="block text-sm font-medium mb-2">Photo</label>

                    <div class="border-2 border-dashed rounded-2xl p-6 text-center"
                        :class="dragging ? 'border-indigo-400 bg-indigo-50/40 dark:border-indigo-500/60 dark:bg-indigo-900/20' :
                            'border-gray-300 dark:border-gray-700'">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Drag & drop a photo here, or
                            <button type="button" class="underline text-indigo-600" @click="$refs.photo.click()">
                                browse
                            </button>
                        </p>
                        <input type="file" name="photo" accept="image/*" x-ref="photo" class="hidden"
                            @change="previewPhoto">
                        <template x-if="photoUrl">
                            <div class="mt-4 flex items-center justify-center gap-4">
                                <img :src="photoUrl"
                                    class="h-24 w-24 rounded-xl object-cover ring-1 ring-gray-200 dark:ring-gray-700"
                                    alt="preview">
                                <button type="button" class="px-3 py-1.5 rounded-xl bg-gray-200 dark:bg-gray-700"
                                    @click="removePhoto">
                                    Remove
                                </button>
                            </div>
                        </template>
                    </div>
                    @error('photo')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Documents: dropzone + list --}}
                <div class="rounded-2xl p-6 bg-white/90 dark:bg-gray-800 shadow-lg" x-data="{ dragging: false }"
                    @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false"
                    @drop.prevent="
                    dragging=false;
                    const incoming = Array.from($event.dataTransfer.files || []);
                    const merged = [...docs, ...incoming];
                    docs = merged;
                    const dt = new DataTransfer();
                    merged.forEach(f => dt.items.add(f));
                    $refs.documents.files = dt.files;
                 ">
                    <label class="block text-sm font-medium mb-2">Documents (multiple)</label>

                    <div class="border-2 border-dashed rounded-2xl p-6 text-center"
                        :class="dragging ? 'border-indigo-400 bg-indigo-50/40 dark:border-indigo-500/60 dark:bg-indigo-900/20' :
                            'border-gray-300 dark:border-gray-700'">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Drag & drop files here, or
                            <button type="button" class="underline text-indigo-600" @click="$refs.documents.click()">
                                choose files
                            </button>
                        </p>
                        <input type="file" name="documents[]" multiple x-ref="documents" class="hidden"
                            @change="previewDocs">
                    </div>

                    <ul class="mt-3 space-y-2" x-show="docs.length" x-cloak>
                        <template x-for="(d, i) in docs" :key="i">
                            <li
                                class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2">
                                <span class="truncate text-sm" x-text="d.name"></span>
                                <button type="button" class="px-3 py-1.5 rounded-xl bg-gray-200 dark:bg-gray-700"
                                    @click="removeDoc(i)">Remove</button>
                            </li>
                        </template>
                    </ul>
                    @error('documents.*')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Progress --}}
                <div x-show="progress>0" class="rounded-2xl bg-white/90 dark:bg-gray-800 shadow-lg p-4" x-cloak>
                    <div class="text-sm mb-2">Uploading... <span x-text="progress + '%'"></span></div>
                    <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-blue-600"
                            :style="`width:${progress}%;`"></div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90 disabled:opacity-60"
                        :disabled="loading">
                        <svg x-show="loading" class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                                opacity="0.3" />
                            <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4" />
                        </svg>
                        <span>Create</span>
                    </button>
                </div>

                {{-- Validation Errors (server-side fallback render) --}}
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
    </div>

    <script>
        function employeeForm() {
            return {
                loading: false,
                progress: 0,
                photoUrl: null,
                docs: [],

                previewPhoto(e) {
                    const file = (e?.target?.files || [])[0];
                    if (!file) {
                        this.photoUrl = null;
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = () => this.photoUrl = reader.result;
                    reader.readAsDataURL(file);
                },
                removePhoto() {
                    const input = this.$refs.photo;
                    if (input) input.value = '';
                    this.photoUrl = null;
                },
                previewDocs(e) {
                    const add = Array.from(e.target.files || []);
                    this.docs = [...this.docs, ...add];
                    const dt = new DataTransfer();
                    this.docs.forEach(f => dt.items.add(f));
                    this.$refs.documents.files = dt.files;
                },
                removeDoc(i) {
                    this.docs.splice(i, 1);
                    const dt = new DataTransfer();
                    this.docs.forEach(f => dt.items.add(f));
                    this.$refs.documents.files = dt.files;
                },

                submit() {
                    this.loading = true;
                    this.progress = 0;

                    const form = document.getElementById('employeeForm');
                    const fd = new FormData(form);

                    const hiddenToken = form.querySelector('input[name=_token]')?.value;
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.getAttribute('action'), true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    if (hiddenToken) xhr.setRequestHeader('X-CSRF-TOKEN', hiddenToken);

                    xhr.upload.addEventListener('progress', (e) => {
                        if (e.lengthComputable) {
                            this.progress = Math.round((e.loaded / e.total) * 100);
                        }
                    });

                    xhr.onload = () => {
                        this.loading = false;

                        if (xhr.status >= 200 && xhr.status < 300) {
                            let res = {};
                            try {
                                res = JSON.parse(xhr.responseText || '{}');
                            } catch {}
                            // Use safe toast; never blocks redirect
                            safeToast('success', res.message ?? 'Employee created');
                            // Redirect immediately (don’t wait for toast)
                            window.location = @json(route('admin.employees.index'));
                            return;
                        }

                        if (xhr.status === 422) {
                            try {
                                const json = JSON.parse(xhr.responseText);
                                const firstMsg = json?.message || Object.values(json?.errors || {})?.[0]?.[0] ||
                                    'Validation error';
                                safeToast('error', firstMsg);
                            } catch {
                                safeToast('error', 'Validation error');
                            }
                        } else {
                            safeToast('error', 'Request failed. Submitting normally...');
                        }

                        // Fallback to normal submit so Laravel renders errors
                        form.removeAttribute('x-on:submit');
                        form.submit();
                    };

                    xhr.onerror = () => {
                        this.loading = false;
                        safeToast('error', 'Network error. Submitting normally...');
                        form.removeAttribute('x-on:submit');
                        form.submit();
                    };

                    xhr.send(fd);
                }
            }
        }
    </script>

@endsection
