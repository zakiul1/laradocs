@php
    $isEdit = isset($factory);
    $action = $isEdit ? route('admin.factories.update', $factory) : route('admin.factories.store');
    $method = $isEdit ? 'PUT' : 'POST';
    $categories = \App\Models\FactoryCategory::forSelect();
    $selectedCategory = old('category_id', $factory->category_id ?? null);
    $subcategories = \App\Models\FactorySubcategory::forSelectByCategory($selectedCategory);
@endphp

<form id="factory-form" action="{{ $action }}" method="post" enctype="multipart/form-data" x-data="factoryForm()"
    x-init="init({{ json_encode([
        'isEdit' => $isEdit,
        'csrf' => csrf_token(),
        'subcategoriesUrl' => route('admin.factories.subcategories.json'),
        'quickCatUrl' => route('admin.factories.quick-create-category'),
        'quickSubUrl' => route('admin.factories.quick-create-subcategory'),
        'redirectIndex' => route('admin.factories.index'),
        // TURN OFF AJAX SUBMIT FOR CLEARER VALIDATION UX
        'ajax' => false,
    ]) }})" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <strong class="font-semibold">Please correct the highlighted fields:</strong>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Top fields --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Name <span
                    class="text-red-600">*</span></label>
            <input id="name" type="text" name="name" required value="{{ old('name', $factory->name ?? '') }}"
                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('name') ? 'name-error' : '' }}"
                class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('name') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}" />
            @error('name')
                <p id="name-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">Phone</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $factory->phone ?? '') }}"
                aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('phone') ? 'phone-error' : '' }}"
                class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('phone') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}" />
            @error('phone')
                <p id="phone-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $factory->email ?? '') }}"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
                class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('email') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}" />
            @error('email')
                <p id="email-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="website" class="mb-1 block text-sm font-medium text-gray-700">Website</label>
            <input id="website" type="url" name="website" placeholder="https://example.com"
                value="{{ old('website', $factory->website ?? '') }}"
                aria-invalid="{{ $errors->has('website') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('website') ? 'website-error' : '' }}"
                class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('website') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}" />
            @error('website')
                <p id="website-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="registration_no" class="mb-1 block text-sm font-medium text-gray-700">Registration No.</label>
            <input id="registration_no" type="text" name="registration_no"
                value="{{ old('registration_no', $factory->registration_no ?? '') }}"
                aria-invalid="{{ $errors->has('registration_no') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('registration_no') ? 'registration_no-error' : '' }}"
                class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('registration_no') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}" />
            @error('registration_no')
                <p id="registration_no-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="total_employees" class="mb-1 block text-sm font-medium text-gray-700">Total Employees</label>
            <input id="total_employees" type="number" name="total_employees" min="0"
                value="{{ old('total_employees', $factory->total_employees ?? 0) }}"
                aria-invalid="{{ $errors->has('total_employees') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('total_employees') ? 'total_employees-error' : '' }}"
                class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('total_employees') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}" />
            @error('total_employees')
                <p id="total_employees-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="lines" class="mb-1 block text-sm font-medium text-gray-700">Lines</label>
            <input id="lines" type="number" name="lines" min="0"
                value="{{ old('lines', $factory->lines ?? 0) }}"
                aria-invalid="{{ $errors->has('lines') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('lines') ? 'lines-error' : '' }}"
                class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('lines') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}" />
            @error('lines')
                <p id="lines-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="notes" class="mb-1 block text-sm font-medium text-gray-700">Notes</label>
            <input id="notes" type="text" name="notes" value="{{ old('notes', $factory->notes ?? '') }}"
                aria-invalid="{{ $errors->has('notes') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('notes') ? 'notes-error' : '' }}"
                class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('notes') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}" />
            @error('notes')
                <p id="notes-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="address" class="mb-1 block text-sm font-medium text-gray-700">Address</label>
        <textarea id="address" name="address" rows="3"
            aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}"
            aria-describedby="{{ $errors->has('address') ? 'address-error' : '' }}"
            class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 {{ $errors->has('address') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}">{{ old('address', $factory->address ?? '') }}</textarea>
        @error('address')
            <p id="address-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Category / Subcategory --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label for="category_id" class="mb-1 block text-sm font-medium text-gray-700">Category</label>
            <div class="flex items-center gap-2">
                <select id="category_id" name="category_id" x-model="categoryId" @change="loadSubcategories()"
                    aria-invalid="{{ $errors->has('category_id') ? 'true' : 'false' }}"
                    aria-describedby="{{ $errors->has('category_id') ? 'category_id-error' : '' }}"
                    class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 {{ $errors->has('category_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}">
                    <option value="">— Select —</option>
                    @foreach ($categories as $id => $name)
                        <option value="{{ $id }}" @selected((string) $id === (string) $selectedCategory)>{{ $name }}</option>
                    @endforeach
                </select>
                <button type="button" @click="openCatModal()"
                    class="inline-flex items-center rounded-lg border border-blue-500 px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50">
                    + New
                </button>
            </div>
            @error('category_id')
                <p id="category_id-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="subcategory_id" class="mb-1 block text-sm font-medium text-gray-700">Subcategory</label>
            <div class="flex items-center gap-2">
                <select id="subcategory_id" name="subcategory_id" x-model="subcategoryId"
                    aria-invalid="{{ $errors->has('subcategory_id') ? 'true' : 'false' }}"
                    aria-describedby="{{ $errors->has('subcategory_id') ? 'subcategory_id-error' : '' }}"
                    class="block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 {{ $errors->has('subcategory_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-500' }}">
                    <option value="">— Select —</option>
                    @foreach ($subcategories as $id => $name)
                        <option value="{{ $id }}" @selected((string) $id === (string) old('subcategory_id', $factory->subcategory_id ?? ''))>{{ $name }}</option>
                    @endforeach
                </select>
                <button type="button" :disabled="!categoryId" @click="openSubModal()"
                    class="inline-flex items-center rounded-lg border border-blue-500 px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-50">
                    + New
                </button>
            </div>
            @error('subcategory_id')
                <p id="subcategory_id-error" class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">Pick Category first to add a Subcategory.</p>
        </div>
    </div>

    {{-- PHOTOS UPLOADER --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <h5 class="text-base font-semibold text-gray-900">Factory Images</h5>
                <p class="text-xs text-gray-500">Drag & drop images (JPG, PNG, WEBP) — multiple allowed. Remove before
                    upload.</p>
            </div>
            <label
                class="inline-flex cursor-pointer items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Choose Images
                <input type="file" accept="image/*" multiple class="hidden"
                    @change="pickFiles($event,'photos')" />
            </label>
        </div>

        <div class="rounded-xl border-2 border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 transition
                    hover:border-blue-400"
            @dragover.prevent="hover=true" @dragleave.prevent="hover=false"
            @drop.prevent="dropFiles($event,'photos')" :class="{ 'bg-blue-50/50 border-blue-400': hover }">
            Drop images here
        </div>

        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4" x-show="photos.length">
            <template x-for="(f,idx) in photos" :key="f.key">
                <div class="relative overflow-hidden rounded-xl border border-gray-200">
                    <img :src="f.preview" alt="" class="h-36 w-full object-cover" />
                    <div class="space-y-0.5 p-2">
                        <div class="truncate text-xs font-medium text-gray-900" x-text="f.file.name"></div>
                        <div class="text-[11px] text-gray-500" x-text="prettySize(f.file.size)"></div>
                    </div>
                    <button type="button"
                        class="absolute right-2 top-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/70 text-white"
                        @click="remove(idx,'photos')">×</button>
                    <div class="h-1 w-full bg-gray-200" x-show="uploading">
                        <div class="h-1 bg-blue-600" :style="`width:${f.progress}%`"></div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Photos validation errors --}}
        @if ($errors->has('photos') || $errors->has('photos.*'))
            <div class="mt-2 rounded-md bg-red-50 p-3">
                <ul class="list-disc pl-5 text-xs text-red-700">
                    @foreach ($errors->get('photos') as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                    @foreach ($errors->get('photos.*') as $arr)
                        @foreach ($arr as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($isEdit && $factory->photos->count())
            <div class="mt-4">
                <label class="mb-2 block text-sm font-medium text-gray-700">Existing Images</label>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                    @foreach ($factory->photos as $p)
                        <div class="relative overflow-hidden rounded-xl border border-gray-200">
                            <img src="{{ $p->url() }}" alt="" class="h-36 w-full object-cover" />
                            <div class="space-y-0.5 p-2">
                                <div class="truncate text-xs font-medium text-gray-900">{{ $p->name }}</div>
                                <div class="text-[11px] text-gray-500">{{ number_format($p->size / 1024, 1) }} KB
                                </div>
                            </div>
                            <label
                                class="absolute inset-x-0 bottom-0 flex items-center gap-2 bg-white/90 p-2 text-xs text-gray-700">
                                <input type="checkbox" name="remove_photo_ids[]" value="{{ $p->id }}"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span>Remove on save</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- DOCUMENTS UPLOADER --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <h5 class="text-base font-semibold text-gray-900">Factory Documents / Certificates</h5>
                <p class="text-xs text-gray-500">PDF, DOC/DOCX, XLS/XLSX, JPG/PNG/WEBP, ZIP — multiple allowed.</p>
            </div>
            <label
                class="inline-flex cursor-pointer items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Choose Files
                <input type="file" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.webp,.zip"
                    class="hidden" @change="pickFiles($event,'documents')" />
            </label>
        </div>

        <div class="rounded-xl border-2 border-dashed border-gray-300 p-6 text-center text-sm text-gray-500 transition
                    hover:border-blue-400"
            @dragover.prevent="hoverDoc=true" @dragleave.prevent="hoverDoc=false"
            @drop.prevent="dropFiles($event,'documents')" :class="{ 'bg-blue-50/50 border-blue-400': hoverDoc }">
            Drop documents here
        </div>

        <div class="mt-3 space-y-2" x-show="documents.length">
            <template x-for="(f,idx) in documents" :key="f.key">
                <div
                    class="grid grid-cols-[1fr_auto] items-center gap-3 rounded-xl border border-gray-200 bg-white p-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                        <strong class="truncate text-sm text-gray-900" x-text="f.file.name"></strong>
                        <small class="text-xs text-gray-500" x-text="prettySize(f.file.size)"></small>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button"
                            class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50"
                            @click="remove(idx,'documents')">
                            Remove
                        </button>
                    </div>
                    <div class="col-span-2 h-1 w-full bg-gray-200" x-show="uploading">
                        <div class="h-1 bg-blue-600" :style="`width:${f.progress}%`"></div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Documents validation errors --}}
        @if ($errors->has('documents') || $errors->has('documents.*'))
            <div class="mt-2 rounded-md bg-red-50 p-3">
                <ul class="list-disc pl-5 text-xs text-red-700">
                    @foreach ($errors->get('documents') as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                    @foreach ($errors->get('documents.*') as $arr)
                        @foreach ($arr as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($isEdit && $factory->documents->count())
            <div class="mt-4">
                <label class="mb-2 block text-sm font-medium text-gray-700">Existing Documents</label>
                <div class="space-y-2">
                    @foreach ($factory->documents as $d)
                        <div
                            class="grid grid-cols-[1fr_auto] items-center gap-3 rounded-xl border border-gray-200 bg-white p-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-block h-2 w-2 rounded-full bg-blue-600"></span>
                                <strong class="truncate text-sm text-gray-900">{{ $d->name }}</strong>
                                <small class="text-xs text-gray-500">{{ $d->mime ?? 'file' }} —
                                    {{ number_format($d->size / 1024, 1) }} KB</small>
                            </div>
                            <label class="flex items-center gap-2 text-xs text-gray-700">
                                <input type="checkbox" name="remove_document_ids[]" value="{{ $d->id }}"
                                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span>Remove on save</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        {{-- IMPORTANT: NORMAL FORM SUBMIT FOR CLEAR, FIELD-BY-FIELD VALIDATION --}}
        <button type="submit"
            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50">
            Save
        </button>
        <a href="{{ route('admin.factories.index') }}"
            class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            Cancel
        </a>

        <div class="ml-2 h-1 w-44 overflow-hidden rounded bg-gray-200" x-show="uploading">
            <div class="h-1 bg-emerald-500" :style="`width:${overall}%`"></div>
        </div>
    </div>

    <!-- Category Modal -->
    <div x-show="showCat" style="display:none;"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4" x-transition>
        <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                <h5 class="text-sm font-semibold text-gray-900">New Category</h5>
                <button type="button" class="text-gray-500 hover:text-gray-700" @click="showCat=false">×</button>
            </div>
            <div class="space-y-3 p-4">
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <input type="text"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    x-model="newCatName">
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-gray-200 px-4 py-3">
                <button type="button"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    @click="showCat=false">Cancel</button>
                <button type="button"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    @click="createCategory()">Create</button>
            </div>
        </div>
    </div>

    <!-- Subcategory Modal -->
    <div x-show="showSub" style="display:none;"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4" x-transition>
        <div class="w-full max-w-md rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                <h5 class="text-sm font-semibold text-gray-900">New Subcategory</h5>
                <button type="button" class="text-gray-500 hover:text-gray-700" @click="showSub=false">×</button>
            </div>
            <div class="space-y-3 p-4">
                <p class="text-sm text-gray-700"><strong>Category:</strong> <span
                        x-text="categoryText() || '—'"></span></p>
                <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                <input type="text"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    x-model="newSubName">
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-gray-200 px-4 py-3">
                <button type="button"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    @click="showSub=false">Cancel</button>
                <button type="button"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                    @click="createSubcategory()">Create</button>
            </div>
        </div>
    </div>
</form>

{{-- Focus the first errored field so users immediately see where to fix --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const firstInvalid = document.querySelector('[aria-invalid="true"]');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstInvalid.focus({
                    preventScroll: true
                });
            }
        });
    </script>
@endif

<script>
    function factoryForm() {
        return {
            // state
            categoryId: '{{ (string) $selectedCategory }}' || '',
            subcategoryId: '{{ (string) old('subcategory_id', $factory->subcategory_id ?? '') }}' || '',
            showCat: false,
            showSub: false,
            newCatName: '',
            newSubName: '',
            photos: [],
            documents: [],
            uploading: false,
            overall: 0,
            hover: false,
            hoverDoc: false,
            cfg: {},

            init(cfg) {
                this.cfg = cfg;
            },

            openCatModal() {
                this.newCatName = '';
                this.showCat = true;
            },
            openSubModal() {
                if (!this.categoryId) return;
                this.newSubName = '';
                this.showSub = true;
            },

            categoryText() {
                const sel = document.querySelector('select[name=category_id]');
                return sel?.selectedOptions?.[0]?.textContent ?? '';
            },

            loadSubcategories() {
                this.subcategoryId = '';
                const subSel = document.querySelector('select[name=subcategory_id]');
                subSel.innerHTML = '<option value="">Loading…</option>';
                fetch(`${this.cfg.subcategoriesUrl}?category_id=${this.categoryId}`)
                    .then(r => r.json()).then(rows => {
                        subSel.innerHTML = '<option value="">— Select —</option>';
                        rows.forEach(r => {
                            const opt = document.createElement('option');
                            opt.value = r.id;
                            opt.textContent = r.name;
                            subSel.appendChild(opt);
                        });
                    });
            },

            // pick/drop
            pickFiles(e, type) {
                const files = Array.from(e.target.files || []);
                this.addFiles(files, type);
                e.target.value = '';
            },
            dropFiles(e, type) {
                const files = Array.from(e.dataTransfer.files || []);
                this.addFiles(files, type);
                this.hover = false;
                this.hoverDoc = false;
            },
            addFiles(files, type) {
                const list = type === 'photos' ? this.photos : this.documents;
                files.forEach(file => {
                    const key = `${type}-${Date.now()}-${Math.random().toString(36).slice(2)}`;
                    const item = {
                        key,
                        file,
                        progress: 0,
                        preview: null
                    };
                    if (type === 'photos') {
                        item.preview = URL.createObjectURL(file);
                    }
                    list.push(item);
                });
            },
            remove(idx, type) {
                const list = type === 'photos' ? this.photos : this.documents;
                const item = list[idx];
                if (item?.preview) URL.revokeObjectURL(item.preview);
                list.splice(idx, 1);
            },

            prettySize(bytes) {
                const kb = bytes / 1024,
                    mb = kb / 1024;
                return mb >= 1 ? `${mb.toFixed(1)} MB` : `${kb.toFixed(1)} KB`;
            },

            async createCategory() {
                if (!this.newCatName.trim()) return;
                const res = await fetch(this.cfg.quickCatUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.cfg.csrf,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.newCatName.trim()
                    })
                });
                const {
                    id,
                    name
                } = await res.json();
                const sel = document.querySelector('select[name=category_id]');
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = name;
                sel.appendChild(opt);
                sel.value = String(id);
                this.categoryId = String(id);
                this.showCat = false;
                this.loadSubcategories();
            },

            async createSubcategory() {
                if (!this.newSubName.trim() || !this.categoryId) return;
                const res = await fetch(this.cfg.quickSubUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.cfg.csrf,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.newSubName.trim(),
                        factory_category_id: Number(this.categoryId)
                    })
                });
                const {
                    id,
                    name
                } = await res.json();
                const sel = document.querySelector('select[name=subcategory_id]');
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = name;
                sel.appendChild(opt);
                sel.value = String(id);
                this.subcategoryId = String(id);
                this.showSub = false;
            },

            // (Optional) AJAX submit kept here but DISABLED by default via cfg.ajax=false
            submitAjax(form) {
                if (!this.cfg.ajax) {
                    // fall back to normal form submit for clearer field errors
                    form.submit();
                    return;
                }

                const fd = new FormData(form);
                this.photos.forEach(p => fd.append('photos[]', p.file));
                this.documents.forEach(d => fd.append('documents[]', d.file));

                if (this.uploading) return;
                this.uploading = true;
                this.overall = 0;

                const xhr = new XMLHttpRequest();
                xhr.open(form.method || 'POST', form.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', this.cfg.csrf);

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const pct = Math.round((e.loaded / e.total) * 100);
                        this.overall = pct;
                        [...this.photos, ...this.documents].forEach(item => item.progress = pct);
                    }
                });

                xhr.onreadystatechange = () => {
                    if (xhr.readyState === 4) {
                        this.uploading = false;
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.redirect) window.location = data.redirect;
                                else window.location = this.cfg.redirectIndex;
                            } catch (_e) {
                                window.location = this.cfg.redirectIndex;
                            }
                        } else {
                            alert('Save failed. Please check errors and try again.');
                        }
                    }
                };

                xhr.send(fd);
            }
        }
    }
</script>
