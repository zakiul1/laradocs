@php
    // mimic the “factory” two-column layout with wide inputs + subtle cards
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="rounded-2xl border bg-white p-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            {{-- LEFT COLUMN --}}
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Name <span class="text-red-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $employee->name) }}"
                        class="w-full rounded-xl border px-3 py-2 @error('name') border-red-500 ring-1 ring-red-200 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Email</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                        class="w-full rounded-xl border px-3 py-2 @error('email') border-red-500 ring-1 ring-red-200 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}"
                        class="w-full rounded-xl border px-3 py-2 @error('phone') border-red-500 ring-1 ring-red-200 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $employee->designation) }}"
                        class="w-full rounded-xl border px-3 py-2 @error('designation') border-red-500 ring-1 ring-red-200 @enderror">
                    @error('designation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Join Date</label>
                        <input type="date" name="join_date" value="{{ old('join_date', $employee->join_date) }}"
                            class="w-full rounded-xl border px-3 py-2 @error('join_date') border-red-500 ring-1 ring-red-200 @enderror">
                        @error('join_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Leave Date</label>
                        <input type="date" name="leave_date" value="{{ old('leave_date', $employee->leave_date) }}"
                            class="w-full rounded-xl border px-3 py-2 @error('leave_date') border-red-500 ring-1 ring-red-200 @enderror">
                        @error('leave_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- FULL-WIDTH ADDRESS --}}
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-medium">Address</label>
                <textarea name="address" rows="3"
                    class="w-full rounded-xl border px-3 py-2 @error('address') border-red-500 ring-1 ring-red-200 @enderror">{{ old('address', $employee->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- FULL-WIDTH PHOTO (normal submit + live preview/remove) --}}
            <div class="md:col-span-2" x-data="{
                file: null,
                preview: {{ $employee->photo_path ? '\'' . Storage::url($employee->photo_path) . '\'' : 'null' }},
                pick(e) {
                    this.file = e.target.files[0] || null;
                    this.preview = this.file ? URL.createObjectURL(this.file) : this.preview;
                },
                clear() {
                    this.file = null;
                    $refs.input.value = '';
                    this.preview = null;
                }
            }">
                <label class="mb-1 block text-sm font-medium">Photo (normal submit)</label>
                <div class="flex items-center gap-3">
                    <input x-ref="input" type="file" name="photo" accept="image/*" @change="pick"
                        class="flex-1 rounded-xl border px-3 py-2 @error('photo') border-red-500 ring-1 ring-red-200 @enderror">
                    <button type="button" class="rounded-xl border px-3 py-2 text-sm hover:bg-gray-50"
                        @click="clear()">Remove</button>
                </div>
                @error('photo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-3 flex items-center gap-3">
                    <template x-if="preview">
                        <img :src="preview" class="h-16 w-16 rounded-lg object-cover" alt="">
                    </template>

                    @if ($employee->photo_path)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="remove_photo" value="1" class="rounded">
                            Remove current photo
                        </label>
                    @endif
                </div>
            </div>

            {{-- FULL-WIDTH DOCUMENTS (normal submit + pre-submit queue/remove) --}}
            <div class="md:col-span-2" x-data="{
                files: [],
                pick(e) {
                    this.add(e.target.files);
                    e.target.value = '';
                },
                add(list) {
                    Array.from(list || []).forEach(f => this.files.push(f));
                    syncInput();
                },
                remove(i) {
                    this.files.splice(i, 1);
                    syncInput();
                },
                human(s) { if (s < 1024) return s + ' B'; if (s < 1024 * 1024) return (s / 1024).toFixed(1) + ' KB'; return (s / 1024 / 1024).toFixed(1) + ' MB'; },
                syncInput() { // rebuild the native FileList via DataTransfer so form submits these files
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f));
                    $refs.real.files = dt.files;
                }
            }">
                <label class="mb-1 block text-sm font-medium">Documents (normal submit)</label>

                <input type="file" multiple @change="pick" class="rounded-xl border px-3 py-2">
                <input type="file" name="documents[]" x-ref="real" class="hidden" multiple>

                <template x-if="files.length">
                    <div class="mt-3 space-y-2">
                        <template x-for="(f,i) in files" :key="i">
                            <div class="flex items-center justify-between rounded-xl border px-3 py-2">
                                <div class="truncate">
                                    <div class="font-medium truncate" x-text="f.name"></div>
                                    <div class="text-xs text-gray-500" x-text="human(f.size)"></div>
                                </div>
                                <button type="button" class="rounded-lg border px-2 py-1 text-xs hover:bg-gray-50"
                                    @click="remove(i)">Remove</button>
                            </div>
                        </template>
                    </div>
                </template>
                @error('documents')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('documents.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('employees.index') }}" class="rounded-xl border px-4 py-2 hover:bg-gray-50">Back</a>
        <button class="rounded-xl bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">Save</button>
    </div>
</form>

{{-- ENHANCED AJAX (only when employee exists) --}}
@if ($employee->id)
    <div class="rounded-2xl border bg-white p-4 mt-6">
        <h3 class="mb-3 font-semibold">Photo — AJAX (drag & drop, progress)</h3>
        <div x-data="photoUploader('{{ route('employees.photo.upload', $employee) }}')" class="rounded-xl border p-4">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="md:col-span-2">
                    <div @dragover.prevent="drag=true" @dragleave.prevent="drag=false"
                        @drop.prevent="handleDrop($event)"
                        :class="drag ? 'border-indigo-400 bg-indigo-50' : 'border-dashed'"
                        class="flex h-28 w-full items-center justify-center rounded-xl border text-sm relative">
                        <div class="text-center">
                            <p class="font-medium">Drop image here or click to choose</p>
                            <input type="file" accept="image/*" @change="pick"
                                class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <template x-if="preview"><img :src="preview" class="h-16 w-16 rounded-lg object-cover"
                            alt=""></template>
                    <div class="ml-auto flex gap-2">
                        <button @click="clear()" type="button"
                            class="rounded-xl border px-3 py-2 text-sm hover:bg-gray-50"
                            :disabled="!file">Remove</button>
                        <button @click="upload" type="button"
                            class="rounded-xl bg-indigo-600 px-3 py-2 text-sm text-white disabled:opacity-50"
                            :disabled="!file || loading">Upload</button>
                    </div>
                </div>
            </div>
            <template x-if="loading">
                <div class="mt-3">
                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                        <div class="h-2 bg-indigo-600" :style="`width:${progress}%`"></div>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">Uploading… <span x-text="progress"></span>%</p>
                </div>
            </template>
            <template x-if="error">
                <p class="mt-2 text-sm text-red-600" x-text="error"></p>
            </template>
        </div>
    </div>

    <div class="rounded-2xl border bg-white p-4 mt-6">
        <h3 class="mb-3 font-semibold">Documents — AJAX (multi-file, progress)</h3>
        <div x-data="docUploader('{{ route('employees.documents.upload', $employee) }}')" class="rounded-xl border p-4">
            <div @dragover.prevent="drag=true" @dragleave.prevent="drag=false" @drop.prevent="dropFiles($event)"
                :class="drag ? 'border-indigo-400 bg-indigo-50' : 'border-dashed'"
                class="flex h-28 w-full items-center justify-center rounded-xl border text-sm relative">
                <div class="text-center">
                    <p class="font-medium">Drop files here or click to choose</p>
                    <p class="text-gray-500">PDF, DOC/X, XLS/X, JPG/PNG (≤ 20 MB each)</p>
                </div>
                <input type="file" multiple @change="pick"
                    class="absolute inset-0 h-full w-full opacity-0 cursor-pointer">
            </div>

            <template x-if="queue.length">
                <div class="mt-4 space-y-2">
                    <template x-for="(item, idx) in queue" :key="item.id">
                        <div class="rounded-xl border p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 truncate">
                                    <div class="font-medium truncate" x-text="item.file.name"></div>
                                    <div class="text-xs text-gray-500" x-text="human(item.file.size)"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        class="rounded-lg border px-2 py-1 text-xs hover:bg-gray-50"
                                        @click="remove(idx)" :disabled="item.loading">Remove</button>
                                    <button type="button"
                                        class="rounded-lg bg-indigo-600 px-2 py-1 text-xs text-white disabled:opacity-50"
                                        @click="uploadOne(idx)" :disabled="item.loading">Upload</button>
                                </div>
                            </div>
                            <template x-if="item.loading">
                                <div class="mt-2">
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-2 bg-indigo-600" :style="`width:${item.progress}%`"></div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-600">Uploading… <span
                                            x-text="item.progress"></span>%</p>
                                </div>
                            </template>
                            <template x-if="item.error">
                                <p class="mt-1 text-xs text-red-600" x-text="item.error"></p>
                            </template>
                        </div>
                    </template>

                    <div class="flex justify-end">
                        <button type="button"
                            class="rounded-xl bg-indigo-600 px-4 py-2 text-white disabled:opacity-50"
                            @click="uploadAll" :disabled="!queue.length || uploadingAny">Upload All</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Existing docs --}}
        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
            @forelse($employee->documents as $doc)
                <div class="flex items-center justify-between rounded-xl border p-3">
                    <div class="truncate">
                        <div class="font-medium truncate">{{ $doc->original_name }}</div>
                        <div class="text-xs text-gray-500">{{ number_format($doc->size / 1024, 1) }} KB •
                            {{ $doc->mime }}</div>
                        <a href="{{ Storage::url($doc->path) }}" class="text-sm text-indigo-600 underline"
                            target="_blank">View / Download</a>
                    </div>
                    <button x-data
                        @click.prevent="$dispatch('confirm-delete', {url: '{{ route('employees.documents.delete', [$employee, $doc]) }}'})"
                        class="rounded-lg border px-3 py-1.5 text-sm text-red-600 hover:bg-red-50">Delete</button>
                </div>
            @empty
                <p class="text-sm text-gray-500">No documents yet.</p>
            @endforelse
        </div>
    </div>
@endif

@push('scripts')
    <script>
        function photoUploader(url) {
            return {
                file: null,
                preview: null,
                loading: false,
                progress: 0,
                error: '',
                drag: false,
                pick(e) {
                    this.file = e.target.files?.[0] || null;
                    this.preview = this.file ? URL.createObjectURL(this.file) : this.preview;
                },
                handleDrop(e) {
                    this.drag = false;
                    const f = e.dataTransfer.files?.[0];
                    if (f) {
                        this.file = f;
                        this.preview = URL.createObjectURL(f);
                    }
                },
                clear() {
                    this.file = null;
                    this.preview = null;
                    this.error = '';
                    this.progress = 0;
                },
                upload() {
                    if (!this.file) return;
                    this.loading = true;
                    this.progress = 0;
                    this.error = '';
                    const form = new FormData();
                    form.append('photo', this.file);
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', url, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.upload.addEventListener('progress', e => {
                        if (e.lengthComputable) {
                            this.progress = Math.round((e.loaded / e.total) * 100);
                        }
                    });
                    xhr.onload = () => {
                        this.loading = false;
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (!res.ok) {
                                this.error = res.message || Object.values(res.errors || {}).flat().join(', ');
                            } else {
                                this.preview = res.path;
                                this.file = null;
                            }
                        } catch {
                            this.error = 'Unexpected response.';
                        }
                    };
                    xhr.onerror = () => {
                        this.loading = false;
                        this.error = 'Network error.';
                    };
                    xhr.send(form);
                }
            }
        }

        function docUploader(url) {
            return {
                queue: [],
                drag: false,
                get uploadingAny() {
                    return this.queue.some(i => i.loading)
                },
                pick(e) {
                    this.addFiles(e.target.files);
                    e.target.value = '';
                },
                dropFiles(e) {
                    this.drag = false;
                    this.addFiles(e.dataTransfer.files);
                },
                addFiles(list) {
                    Array.from(list || []).forEach(f => this.queue.push({
                        id: Date.now() + Math.random(),
                        file: f,
                        progress: 0,
                        loading: false,
                        error: ''
                    }))
                },
                remove(i) {
                    this.queue.splice(i, 1);
                },
                uploadOne(i) {
                    const item = this.queue[i];
                    if (!item || item.loading) return;
                    item.loading = true;
                    item.progress = 0;
                    item.error = '';
                    const form = new FormData();
                    form.append('file', item.file);
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', url, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                    xhr.upload.addEventListener('progress', e => {
                        if (e.lengthComputable) {
                            item.progress = Math.round((e.loaded / e.total) * 100);
                        }
                    });
                    xhr.onload = () => {
                        item.loading = false;
                        try {
                            const res = JSON.parse(xhr.responseText);
                            if (!res.ok) {
                                item.error = res.errors ? Object.values(res.errors).flat().join(', ') : (res.message ||
                                    'Upload failed');
                            } else {
                                this.queue.splice(i, 1);
                                window.location.reload();
                            }
                        } catch {
                            item.error = 'Unexpected response';
                        }
                    };
                    xhr.onerror = () => {
                        item.loading = false;
                        item.error = 'Network error';
                    };
                    xhr.send(form);
                },
                async uploadAll() {
                    for (let i = 0; i < this.queue.length; i++) {
                        await new Promise(r => {
                            const int = setInterval(() => {
                                if (!this.queue[i] || !this.queue[i].loading) {
                                    clearInterval(int);
                                    r();
                                }
                            }, 120);
                            this.uploadOne(i);
                        });
                    }
                },
                human(size) {
                    if (size < 1024) return `${size} B`;
                    if (size < 1024 * 1024) return `${(size/1024).toFixed(1)} KB`;
                    return `${(size/1024/1024).toFixed(1)} MB`;
                }
            }
        }
    </script>
@endpush

{{-- Reusable delete modal --}}
<x-delete-modal />
