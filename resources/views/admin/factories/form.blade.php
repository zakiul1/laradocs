{{-- resources/views/admin/factories/form.blade.php --}}
@extends('layouts.app')
@section('title', ($isEdit ? 'Edit Factory' : 'Add Factory') . ' — Siatex Docs')
@section('crumb', 'Factories / ' . ($isEdit ? 'Edit' : 'Add'))

@section('content')
    {{-- Back --}}
    <div class="mb-4">
        <a href="{{ route('admin.factories.index') }}"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            Back
        </a>
    </div>

    <x-section-title :title="$isEdit ? 'Edit Factory' : 'Add Factory'" />

    @if ($errors->any())
        <x-alert type="error">{{ $errors->first() }}</x-alert>
    @endif
    @if (session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    <x-card x-data="factoryForm(@js($categories), @js($selected), @js($isEdit ? $factory->toArray() : null))" x-init="init()" stickyFooter :bodyClass="'p-6 pb-28'">
        <form id="factory-form" x-on:submit="submitting = true" method="POST"
            action="{{ $isEdit ? route('admin.factories.update', $factory) : route('admin.factories.store') }}"
            enctype="multipart/form-data" class="space-y-8">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            {{-- Basic Info --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group">
                    <x-label value="Factory Name" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="name" required :value="old('name', $factory->name)"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                                    focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="group">
                    <x-label value="Phone (BD)" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="phone" :value="old('phone', $factory->phone)"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                                    focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition"
                        placeholder="+8801XXXXXXXXX or 01XXXXXXXXX" />
                    @error('phone')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="group md:col-span-2">
                    <x-label value="Address" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="address" :value="old('address', $factory->address)"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                                    focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                </div>

                <div class="group">
                    <x-label value="Garments Lines" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <x-input name="lines" type="number" min="0" step="1" :value="old('lines', $factory->lines)"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                                    focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition" />
                    @error('lines')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="group md:col-span-2">
                    <x-label value="Notes" class="text-[0.95rem] group-focus-within:text-gray-900" />
                    <textarea name="notes"
                        class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                                     focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition"
                        rows="3">{{ old('notes', $factory->notes) }}</textarea>
                    @error('notes')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Global Categories (scope=factory): choose Root, then select children (multi) --}}
            <div class="md:col-span-2">
                <div x-data="categoryPicker()" class="space-y-4">
                    <div class="group">
                        <x-label value="Category group (root)" class="text-[0.95rem]" />
                        <select x-model.number="parentId"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5
                                       focus:ring-4 focus:ring-gray-900/10 focus:border-gray-900 transition">
                            <option value="">— Select a root category —</option>
                            <template x-for="p in parents" :key="p.id">
                                <option :value="p.id" x-text="p.name"></option>
                            </template>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Scope: <code>factory</code></p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2" x-show="parentId">
                        <template x-for="child in children" :key="child.id">
                            <label class="flex items-center gap-2 rounded-lg border px-3 py-2 bg-gray-50">
                                <input type="checkbox" class="rounded cursor-pointer" :value="child.id"
                                    @change="toggle(child.id)" :checked="isChecked(child.id)">
                                <span x-text="child.name"></span>
                            </label>
                        </template>
                    </div>

                    {{-- Hidden inputs to submit the selection --}}
                    <template x-for="id in selected" :key="'cat-' + id">
                        <input type="hidden" name="category_ids[]" :value="id">
                    </template>

                    @error('category_ids')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    @error('category_ids.*')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Photos --}}
            <div class="group">
                <x-label value="Factory Photos (multiple)" class="text-[0.95rem] group-focus-within:text-gray-900" />
                <div class="rounded-2xl border-2 border-dashed p-6 bg-white">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <p class="text-sm text-gray-600">JPG/PNG/WebP up to 10MB each.</p>
                        <label
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            <span>Add photos</span>
                            <input type="file" class="hidden" multiple name="photos[]" accept="image/*"
                                @change="addPhotos($event)">
                        </label>
                    </div>

                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                        {{-- existing (edit) --}}
                        @if ($isEdit)
                            @foreach ($factory->photos as $p)
                                <label class="relative rounded-xl overflow-hidden border block">
                                    <img src="{{ asset('storage/' . $p->path) }}" class="w-full h-28 object-cover"
                                        alt="">
                                    <input type="checkbox" name="remove_photos[]" value="{{ $p->id }}"
                                        title="Remove on save" class="absolute top-2 right-2 rounded cursor-pointer">
                                </label>
                            @endforeach
                        @endif

                        {{-- new previews --}}
                        <template x-for="(ph, i) in photoPreviews" :key="'ph-' + i">
                            <div class="relative rounded-xl overflow-hidden border">
                                <img :src="ph" class="w-full h-28 object-cover" alt="">
                                <button type="button"
                                    class="absolute top-2 right-2 text-xs px-2 py-1 rounded-md border bg-white/90 cursor-pointer"
                                    @click="removeNewPhoto(i)">
                                    Remove
                                </button>
                            </div>
                        </template>
                    </div>

                    <p x-show="loadingPhotos" class="text-sm text-gray-500 mt-3 inline-flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity=".25"
                                stroke-width="4" />
                            <path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="4" />
                        </svg>
                        Reading photos…
                    </p>
                </div>
                @error('photos')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                @error('photos.*')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Documents --}}
            <div class="group">
                <x-label value="Documents / Certificates" class="text-[0.95rem] group-focus-within:text-gray-900" />
                <div class="rounded-2xl border-2 border-dashed p-6 bg-white">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <p class="text-sm text-gray-600">PDF/Word/Excel, up to 20MB each.</p>
                        <label
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                            <span>Add documents</span>
                            <input type="file" class="hidden" multiple name="documents[]"
                                accept=".pdf,.doc,.docx,.xls,.xlsx" @change="addDocs($event)">
                        </label>
                    </div>

                    {{-- existing docs --}}
                    @if ($isEdit)
                        <div class="mt-4 space-y-2">
                            @foreach ($factory->documents as $d)
                                <label class="flex items-center justify-between rounded-lg border bg-gray-50 px-3 py-2">
                                    <a href="{{ asset('storage/' . $d->path) }}" target="_blank"
                                        class="text-sm underline cursor-pointer">
                                        {{ $d->original_name }}
                                    </a>
                                    <input type="checkbox" name="remove_docs[]" value="{{ $d->id }}"
                                        class="rounded cursor-pointer" title="Remove on save">
                                </label>
                            @endforeach
                        </div>
                    @endif

                    {{-- new docs --}}
                    <div class="mt-4 space-y-2">
                        <template x-for="(f,i) in docFiles" :key="'doc-' + i">
                            <div class="flex items-center justify-between rounded-lg border bg-gray-50 px-3 py-2">
                                <span class="text-sm truncate" x-text="f.name"></span>
                                <button type="button"
                                    class="text-xs px-2 py-1 rounded-md border hover:bg-white cursor-pointer"
                                    @click="removeNewDoc(i)">
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
                            Reading documents…
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

        {{-- Sticky footer --}}
        <x-slot:footer>
            <div class="flex items-center justify-between">
                <a href="{{ route('admin.factories.index') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border bg-white hover:bg-gray-50 text-sm cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Back
                </a>
                <button type="submit" form="factory-form"
                    class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition cursor-pointer"
                    :disabled="submitting" :class="submitting ? 'opacity-70 pointer-events-none' : ''">
                    <span x-show="!submitting">{{ $isEdit ? 'Save Changes' : 'Save Factory' }}</span>
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
        function factoryForm(categories, selectedIds, existing) {
            return {
                submitting: false,
                // categories from PHP (scope: factory), including children
                cats: categories || [],
                // selected category IDs for edit
                selected: Array.isArray(selectedIds) ? [...selectedIds] : [],
                // upload UI
                photoPreviews: [],
                docFiles: [],
                loadingPhotos: false,
                loadingDocs: false,

                init() {},

                // Photo helpers
                addPhotos(e) {
                    this.loadingPhotos = true;
                    const files = Array.from(e.target.files);
                    files.forEach(f => {
                        const r = new FileReader();
                        r.onload = () => this.photoPreviews.push(r.result);
                        r.readAsDataURL(f);
                    });
                    setTimeout(() => this.loadingPhotos = false, 200);
                },
                removeNewPhoto(i) {
                    const input = document.querySelector('input[name="photos[]"]');
                    const list = Array.from(input.files);
                    list.splice(i, 1);
                    const bag = new DataTransfer();
                    list.forEach(f => bag.items.add(f));
                    input.files = bag.files;
                    this.photoPreviews.splice(i, 1);
                },

                // Document helpers
                addDocs(e) {
                    this.loadingDocs = true;
                    const files = Array.from(e.target.files);
                    this.docFiles.push(...files);
                    setTimeout(() => this.loadingDocs = false, 200);
                },
                removeNewDoc(i) {
                    const input = document.querySelector('input[name="documents[]"]');
                    const list = Array.from(input.files);
                    list.splice(i, 1);
                    const bag = new DataTransfer();
                    list.forEach(f => bag.items.add(f));
                    input.files = bag.files;
                    this.docFiles.splice(i, 1);
                },
            }
        }

        function categoryPicker() {
            return {
                parentId: null,
                // read from the parent x-data scope (factoryForm)
                get parents() {
                    // roots: parent_id === null
                    return this.$parent.cats.filter(c => c.parent_id === null);
                },
                get children() {
                    return this.$parent.cats.filter(c => c.parent_id === this.parentId);
                },
                get selected() {
                    return this.$parent.selected;
                },
                toggle(id) {
                    const i = this.selected.indexOf(id);
                    if (i === -1) this.selected.push(id);
                    else this.selected.splice(i, 1);
                },
                isChecked(id) {
                    return this.selected.includes(id);
                }
            }
        }
    </script>
@endsection
