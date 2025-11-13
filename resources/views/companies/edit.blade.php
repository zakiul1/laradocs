@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Company</h1>
            <a href="{{ route('admin.companies.index') }}"
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                Back
            </a>
        </header>

        <form id="companyEditForm" x-data="companyEdit()" x-on:submit.prevent="submit"
            action="{{ route('admin.companies.update', $company) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- COLUMN 1 --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Company Name <span class="text-red-500">*</span>
                        </label>
                        <input name="company_name" required type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('company_name', $company->company_name) }}">
                        @error('company_name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Short Name / Code
                        </label>
                        <input name="name" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('name', $company->name) }}">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input name="email" type="email"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('email', $company->email) }}">
                        @error('email')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- COLUMN 2 --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input name="phone" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('phone', $company->phone) }}">
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Contact Person</label>
                        <input name="contact_person" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('contact_person', $company->contact_person) }}">
                        @error('contact_person')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Website</label>
                        <input name="website" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('website', $company->website) }}">
                        @error('website')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- CATEGORY with real-time create --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Category</label>

                        <div class="flex items-center gap-2">
                            {{-- Normal Blade select, handles CURRENT selection --}}
                            <select name="company_category_id" x-ref="categorySelect"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-sm">
                                <option value="">— None —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('company_category_id', $company->company_category_id) == $cat->id)>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="button"
                                class="px-3 py-2 rounded-2xl bg-gray-100 hover:bg-gray-200 text-xs text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                                @click="openCategoryCreator">
                                + New
                            </button>
                        </div>

                        {{-- quick-create panel --}}
                        <div x-show="showCategoryCreator" x-cloak class="mt-2 space-y-2">
                            <input type="text" x-model="newCategoryName"
                                placeholder="New category name (e.g. Courier, Supplier)"
                                class="w-full rounded-2xl border border-dashed border-gray-300 dark:border-gray-600 bg-white/90 dark:bg-gray-800 px-4 py-2 text-sm">
                            <div class="flex gap-2 justify-end">
                                <button type="button"
                                    class="px-3 py-1.5 rounded-2xl text-xs bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600"
                                    @click="cancelCategoryCreate">
                                    Cancel
                                </button>
                                <button type="button"
                                    class="px-3 py-1.5 rounded-2xl text-xs text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90"
                                    @click="saveCategory">
                                    Save
                                </button>
                            </div>
                        </div>

                        @error('company_category_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- COLUMN 3 --}}
                <div class="space-y-4 md:col-span-1">
                    <div>
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <textarea name="address" rows="4"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Note</label>
                        <textarea name="note" rows="4"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('note', $company->note) }}</textarea>
                        @error('note')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
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
            function companyEdit() {
                return {
                    showCategoryCreator: false,
                    newCategoryName: '',

                    openCategoryCreator() {
                        this.showCategoryCreator = true;
                        this.newCategoryName = '';
                    },

                    cancelCategoryCreate() {
                        this.showCategoryCreator = false;
                        this.newCategoryName = '';
                    },

                    saveCategory() {
                        const name = this.newCategoryName.trim();
                        if (!name) {
                            window.safeToast?.('error', 'Category name is required.');
                            return;
                        }

                        fetch(@json(route('admin.companies.categories.quick-create')), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                },
                                body: JSON.stringify({
                                    name
                                }),
                            })
                            .then(r => r.json())
                            .then(json => {
                                if (json?.data?.id) {
                                    // Add new <option> to the select and select it
                                    const select = this.$refs.categorySelect;
                                    const opt = document.createElement('option');
                                    opt.value = json.data.id;
                                    opt.text = json.data.name;
                                    select.appendChild(opt);
                                    select.value = json.data.id;

                                    this.newCategoryName = '';
                                    this.showCategoryCreator = false;
                                    window.safeToast?.('success', json.message ?? 'Category created.');
                                } else {
                                    window.safeToast?.('error', json?.message ?? 'Could not create category.');
                                }
                            })
                            .catch(() => {
                                window.safeToast?.('error', 'Request failed.');
                            });
                    },

                    submit() {
                        const form = document.getElementById('companyEditForm');
                        const fd = new FormData(form);
                        fd.append('_method', 'PUT');

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
                                window.safeToast?.('success', res.message ?? 'Company updated');
                                window.location = @json(route('admin.companies.index'));
                                return;
                            }

                            if (xhr.status === 422) {
                                try {
                                    const json = JSON.parse(xhr.responseText);
                                    const firstMsg =
                                        json?.message ||
                                        Object.values(json?.errors || {})?.[0]?.[0] ||
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
                    },
                };
            }
        </script>
    @endpush
@endsection
