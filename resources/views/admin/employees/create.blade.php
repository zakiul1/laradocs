@extends('layouts.app')
@section('title', 'Add Employee — Siatex Docs')
@section('crumb', 'Employees / Add')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.employees.index') }}"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            Back
        </a>
    </div>

    <x-section-title :title="'Add Employee'" />

    @if ($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <x-card x-data="employeeForm()" x-init="init()" stickyFooter :bodyClass="'p-6 pb-28'">
        <form id="employee-create-form" x-on:submit="submitting=true" method="POST"
            action="{{ route('admin.employees.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- Basic Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div class="group">
                    <x-label for="name" value="Full Name" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="name" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="group">
                    <x-label for="phone" value="Phone (BD)" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="phone" placeholder="+8801XXXXXXXXX or 01XXXXXXXXX" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('phone')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="group">
                    <x-label for="email" value="Email" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="email" type="email" required
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="group">
                    <x-label for="address" value="Address" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="address"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                </div>

                {{-- Join date --}}
                <div class="group">
                    <x-label for="join_date" value="Join Date" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="join_date" type="date"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                </div>

                {{-- Leave date --}}
                <div class="group">
                    <x-label for="leave_date" value="Leave Date" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="leave_date" type="date"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('leave_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Photo uploader --}}
            <div class="group">
                <x-label for="photo" value="Photo" class="text-[0.95rem] group-focus-within:text-gray-900" />
                <div class="flex items-center gap-5">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" class="w-16 h-16 rounded-full object-cover border" alt="Preview">
                    </template>
                    <template x-if="!photoPreview">
                        <div class="w-16 h-16 rounded-full bg-gray-100 grid place-items-center border">
                            <svg class="w-6 h-6 text-gray-400" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </div>
                    </template>

                    <div class="flex items-center gap-3">
                        <label
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span>Choose Photo</span>
                            <input type="file" class="hidden" name="photo" accept="image/*"
                                @change="previewPhoto($event)">
                        </label>

                        <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                            x-show="photoPreview" @click="clearPhoto()">
                            Remove
                        </button>

                        <span x-show="loadingPhoto" class="inline-flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25"
                                    stroke-width="4" />
                                <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4" />
                            </svg>
                            Reading…
                        </span>
                    </div>
                </div>
                @error('photo')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Documents uploader --}}
            <div class="group" x-data="{ dragging: false }" @dragover.prevent="dragging = true"
                @dragleave.prevent="dragging = false" @drop.prevent="dropFiles($event)">
                <x-label value="Documents" class="text-[0.95rem] group-focus-within:text-gray-900" />
                <div class="rounded-2xl border-2 border-dashed p-6 transition bg-white"
                    :class="dragging ? 'border-gray-900 bg-gray-50' : 'border-gray-300'">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <p class="text-sm text-gray-600">PDF, Word, Excel, or images. Max 20MB each.</p>
                        <label
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                            <span>Add files</span>
                            <input type="file" class="hidden" name="documents[]" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" @change="addFiles($event)">
                        </label>
                    </div>

                    <div class="mt-4 space-y-2">
                        <template x-for="(f, i) in files" :key="i">
                            <div class="flex items-center justify-between rounded-lg border bg-gray-50 px-3 py-2">
                                <div class="min-w-0">
                                    <p class="text-sm truncate" x-text="f.name"></p>
                                    <p class="text-xs text-gray-500" x-text="formatSize(f.size)"></p>
                                </div>
                                <button type="button"
                                    class="text-xs px-2 py-1 rounded-md border hover:bg-white cursor-pointer"
                                    @click="removeFile(i)">
                                    Remove
                                </button>
                            </div>
                        </template>

                        <p x-show="loadingDocs" class="text-sm text-gray-500 inline-flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25"
                                    stroke-width="4" />
                                <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4" />
                            </svg>
                            Adding files…
                        </p>

                        <p x-show="files.length" class="text-xs text-gray-500">
                            <span x-text="files.length"></span> file(s), total <span
                                x-text="formatSize(totalSize)"></span>
                        </p>
                    </div>
                </div>
                @error('documents')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                @error('documents.*')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </form>

        {{-- Sticky Save --}}
        <x-slot:footer>
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.employees.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Back
                </a>
                <button type="submit" form="employee-create-form"
                    class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition cursor-pointer"
                    :disabled="submitting" :class="submitting ? 'opacity-70 pointer-events-none' : ''">
                    <span x-show="!submitting">Save Employee</span>
                    <span x-show="submitting" class="inline-flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25"
                                stroke-width="4" />
                            <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4" />
                        </svg>
                        Saving…
                    </span>
                </button>
            </div>
        </x-slot:footer>
    </x-card>

    {{-- Alpine helpers --}}
    <script>
        function employeeForm() {
            return {
                submitting: false,
                loadingPhoto: false,
                loadingDocs: false,
                photoPreview: null,
                files: [],
                totalSize: 0,
                init() {},
                previewPhoto(e) {
                    const [file] = e.target.files;
                    if (!file) return;
                    this.loadingPhoto = true;
                    const reader = new FileReader();
                    reader.onload = () => {
                        this.photoPreview = reader.result;
                        this.loadingPhoto = false;
                    };
                    reader.readAsDataURL(file);
                },
                clearPhoto() {
                    this.photoPreview = null;
                    document.querySelector('input[name="photo"]').value = '';
                },
                addFiles(e) {
                    this.loadingDocs = true;
                    for (const f of e.target.files) {
                        this.files.push(f);
                        this.totalSize += f.size;
                    }
                    setTimeout(() => this.loadingDocs = false, 150); // small UI delay
                },
                dropFiles(e) {
                    const dt = e.dataTransfer;
                    const input = document.querySelector('input[name="documents[]"]');
                    const bag = new DataTransfer();

                    for (const f of input.files) {
                        bag.items.add(f);
                    }
                    for (const f of dt.files) {
                        bag.items.add(f);
                        this.files.push(f);
                        this.totalSize += f.size;
                    }

                    input.files = bag.files;
                    this.dragging = false;
                },
                removeFile(i) {
                    const input = document.querySelector('input[name="documents[]"]');
                    const list = Array.from(input.files);
                    this.totalSize -= this.files[i]?.size || 0;
                    this.files.splice(i, 1);
                    list.splice(i, 1);

                    const bag = new DataTransfer();
                    for (const f of list) {
                        bag.items.add(f);
                    }
                    input.files = bag.files;
                },
                formatSize(s) {
                    if (s < 1024) return s + ' B';
                    if (s < 1024 * 1024) return (s / 1024).toFixed(1) + ' KB';
                    return (s / 1024 / 1024).toFixed(1) + ' MB';
                }
            }
        }
    </script>
@endsection
