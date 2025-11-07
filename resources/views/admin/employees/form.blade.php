@props([
    'employee' => null,
    'isEdit' => false,
])

{{-- Shared Employee Form --}}
<div x-data="employeeForm()" x-init="init(@js($employee?->photoUrl()), @js($isEdit), @js($employee?->documents?->map->only(['id', 'original_name', 'path']) ?? []))" class="space-y-10">

    {{-- ===== Personal Information ===== --}}
    <section class="rounded-xl border bg-white shadow-sm">
        <header class="px-5 md:px-7 py-3.5 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
        </header>

        <div class="p-5 md:p-7 grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-7">

            {{-- Full Name --}}
            <div>
                <x-label for="name" value="Full Name *" />
                <x-input name="name" required :value="old('name', $employee->name ?? '')"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Designation --}}
            <div>
                <x-label for="designation" value="Designation" />
                <x-input name="designation" :value="old('designation', $employee->designation ?? '')"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
                @error('designation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Join Date --}}
            <div>
                <x-label for="join_date" value="Join Date" />
                <x-input name="join_date" type="date" :value="old('join_date', optional($employee->join_date ?? null)?->format('Y-m-d'))"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
            </div>

            {{-- Leave Date --}}
            <div>
                <x-label for="leave_date" value="Leave Date" />
                <x-input name="leave_date" type="date" :value="old('leave_date', optional($employee->leave_date ?? null)?->format('Y-m-d'))"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
                @error('leave_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- ===== Contact Information ===== --}}
    <section class="rounded-xl border bg-white shadow-sm">
        <header class="px-5 md:px-7 py-3.5 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Contact Information</h3>
        </header>

        <div class="p-5 md:p-7 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-7">
            {{-- Email --}}
            <div>
                <x-label for="email" value="E-mail *" />
                <x-input name="email" type="email" required :value="old('email', $employee->email ?? '')"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mobile Number --}}
            <div>
                <x-label for="phone" value="Mobile Number *" />
                <x-input name="phone" required placeholder="+8801XXXXXXXXX or 01XXXXXXXXX" :value="old('phone', $employee->phone ?? '')"
                    class="mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- ===== Address ===== --}}
    <section class="rounded-xl border bg-white shadow-sm">
        <header class="px-5 md:px-7 py-3.5 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Address</h3>
        </header>

        <div class="p-5 md:p-7">
            <x-label for="address" value="Address" />
            <x-input name="address" :value="old('address', $employee->address ?? '')"
                class="mt-1 w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500" />
        </div>
    </section>

    {{-- ===== Photo ===== --}}
    <section class="rounded-xl border bg-white shadow-sm">
        <header class="px-5 md:px-7 py-3.5 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Photo</h3>
        </header>

        <div class="p-5 md:p-7">
            <div class="flex items-center gap-5">
                <template x-if="photoPreview">
                    <img :src="photoPreview" class="size-16 rounded-full object-cover border border-gray-200"
                        alt="Preview">
                </template>
                <template x-if="!photoPreview">
                    <div class="size-16 rounded-full bg-gray-100 grid place-items-center border border-gray-200">
                        <svg class="size-6 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                        </svg>
                    </div>
                </template>

                <div class="flex items-center gap-3">
                    <label
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer transition">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                        </svg>
                        <span x-text="isEdit ? 'Change Photo' : 'Choose Photo'"></span>
                        <input type="file" class="hidden" name="photo" accept="image/*"
                            @change="previewPhoto($event)">
                    </label>

                    {{-- Remove toggle (edit mode) --}}
                    <template x-if="isEdit">
                        <div>
                            <input type="hidden" name="remove_photo" x-model="removePhoto">
                            <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline"
                                @click="toggleRemovePhoto()" x-text="removePhoto ? 'Undo Remove' : 'Remove'">
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            @error('photo')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </section>

    {{-- ===== Documents ===== --}}
    <section class="rounded-xl border bg-white shadow-sm">
        <header class="px-5 md:px-7 py-3.5 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Documents</h3>
        </header>

        <div class="p-5 md:p-7 space-y-7">

            {{-- Existing documents (edit mode) --}}
            <template x-if="isEdit">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Existing documents</h4>
                    <div class="space-y-2" x-show="existingDocs.length">
                        <template x-for="d in existingDocs" :key="d.id">
                            <label class="flex items-center justify-between rounded-lg bg-gray-50 border px-3 py-2.5">
                                <span class="text-sm truncate max-w-[180px]" x-text="d.original_name"></span>
                                <span class="flex items-center gap-2">
                                    <a :href="storageUrl(d.path)" target="_blank"
                                        class="text-xs underline text-primary-600 hover:text-primary-800">View</a>
                                    <input type="checkbox" name="remove_docs[]" :value="d.id"
                                        class="rounded size-4 text-primary-600 focus:ring-primary-500">
                                    <span class="text-xs text-gray-600">Remove</span>
                                </span>
                            </label>
                        </template>
                        <p class="text-sm text-gray-500" x-show="!existingDocs.length">No documents uploaded.</p>
                    </div>
                </div>
            </template>

            {{-- Add / Upload new files --}}
            <div x-data="{ files: [] }">
                <div class="rounded-2xl border-2 border-dashed p-6 bg-white transition hover:border-gray-400">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <p class="text-sm text-gray-600">PDF, Word, Excel, or images. Max 20 MB each.</p>
                        <label
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer transition">
                            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M12 5v14M5 12h14" stroke-linecap="round" />
                            </svg>
                            <span>Add files</span>
                            <input type="file" class="hidden" name="documents[]" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                @change="files = [...$event.target.files]">
                        </label>
                    </div>

                    <ul class="mt-4 space-y-2" x-show="files.length">
                        <template x-for="(f,i) in files" :key="i">
                            <li class="flex items-center justify-between rounded-lg border bg-gray-50 px-3 py-2">
                                <span class="text-sm truncate max-w-[200px]" x-text="f.name"></span>
                                <span class="text-xs text-gray-500" x-text="formatSize(f.size)"></span>
                            </li>
                        </template>
                        <p class="mt-2 text-xs text-gray-500" x-show="files.length">
                            <span x-text="files.length"></span> file(s) selected
                        </p>
                    </ul>
                </div>

                @error('documents')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('documents.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- Alpine helpers (unchanged logic, minor class tweaks) --}}
    <script>
        function employeeForm() {
            return {
                isEdit: false,
                photoPreview: null,
                removePhoto: false,
                existingDocs: [],

                init(photoUrl = null, isEdit = false, docs = []) {
                    this.isEdit = isEdit;
                    this.photoPreview = photoUrl || null;
                    this.existingDocs = docs || [];
                },

                previewPhoto(e) {
                    const [file] = e.target.files;
                    if (!file) return;
                    const r = new FileReader();
                    r.onload = () => {
                        this.photoPreview = r.result;
                        this.removePhoto = false;
                    };
                    r.readAsDataURL(file);
                },

                toggleRemovePhoto() {
                    this.removePhoto = !this.removePhoto;
                    if (this.removePhoto) this.photoPreview = null;
                },

                storageUrl(p) {
                    return '{{ asset('storage') }}/' + p;
                },

                formatSize(s) {
                    if (s < 1024) return s + ' B';
                    if (s < 1024 * 1024) return (s / 1024).toFixed(1) + ' KB';
                    return (s / 1024 / 1024).toFixed(1) + ' MB';
                }
            };
        }
    </script>
</div>
