@extends('layouts.app')
@section('title', 'Edit Employee — Siatex Docs')
@section('crumb', 'Employees / Edit')

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

    <x-section-title :title="'Edit Employee'" />

    @if (session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif
    @if ($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif

    <x-card x-data="employeeEditForm()" x-init="init('{{ $employee->photoUrl() }}')" stickyFooter :bodyClass="'p-6 pb-28'">
        <form id="employee-edit-form" x-on:submit="submitting=true" method="POST"
            action="{{ route('admin.employees.update', $employee) }}" enctype="multipart/form-data" class="space-y-8">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group">
                    <x-label for="name" value="Full Name" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="name" required :value="old('name', $employee->name)"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="group">
                    <x-label for="phone" value="Phone (BD)" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="phone" required :value="old('phone', $employee->phone)"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('phone')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="group">
                    <x-label for="email" value="Email" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="email" type="email" required :value="old('email', $employee->email)"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('email')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="group">
                    <x-label for="address" value="Address" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="address" :value="old('address', $employee->address)"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                </div>

                <div class="group">
                    <x-label for="join_date" value="Join date" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="join_date" type="date" :value="old('join_date', optional($employee->join_date)->format('Y-m-d'))"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                </div>

                <div class="group">
                    <x-label for="leave_date" value="Leave date" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="leave_date" type="date" :value="old('leave_date', optional($employee->leave_date)->format('Y-m-d'))"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                               focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('leave_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Photo --}}
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
                            <span>Change Photo</span>
                            <input type="file" class="hidden" name="photo" accept="image/*"
                                @change="previewPhoto($event)">
                        </label>

                        <input type="hidden" name="remove_photo" x-model="removePhoto">
                        <button type="button" class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                            @click="toggleRemovePhoto()" x-text="removePhoto ? 'Undo Remove' : 'Remove'"></button>
                    </div>
                </div>
                @error('photo')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Existing docs --}}
            <div class="group">
                <x-label value="Existing documents" class="text-[0.95rem] group-focus-within:text-gray-900" />
                <div class="space-y-2">
                    @forelse($employee->documents as $doc)
                        <label class="flex items-center justify-between bg-gray-50 border rounded-lg px-3 py-2">
                            <span class="text-sm truncate">{{ $doc->original_name }}</span>
                            <span class="flex items-center gap-2">
                                <a href="{{ asset('storage/' . $doc->path) }}" target="_blank"
                                    class="text-xs underline cursor-pointer">View</a>
                                <input type="checkbox" name="remove_docs[]" value="{{ $doc->id }}"
                                    class="rounded cursor-pointer">
                                <span class="text-xs">Remove</span>
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500">No documents uploaded.</p>
                    @endforelse
                </div>
            </div>

            {{-- Add new documents --}}
            <div x-data="{ files: [] }" class="group">
                <x-label value="Add documents" class="text-[0.95rem] group-focus-within:text-gray-900" />
                <label
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <span>Browse…</span>
                    <input type="file" class="hidden" name="documents[]" multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" @change="files=[...$event.target.files]">
                </label>
                <ul class="mt-3 space-y-2" x-show="files.length">
                    <template x-for="(f,i) in files" :key="i">
                        <li class="text-sm text-gray-600" x-text="f.name"></li>
                    </template>
                </ul>
                @error('documents')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                @error('documents.*')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </form>

        {{-- Sticky footer --}}
        <x-slot:footer>
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.employees.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Back
                </a>
                <button type="submit" form="employee-edit-form"
                    class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition cursor-pointer"
                    :disabled="submitting" :class="submitting ? 'opacity-70 pointer-events-none' : ''">
                    <span x-show="!submitting">Save Changes</span>
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

    <script>
        function employeeEditForm() {
            return {
                submitting: false,
                photoPreview: null,
                removePhoto: false,
                init(existing) {
                    this.photoPreview = existing || null;
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
                }
            }
        }
    </script>
@endsection
