@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Add Bank</h1>
            <a href="{{ route('admin.banks.index') }}"
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                Back
            </a>
        </header>

        <form id="bankCreateForm" x-data="bankCreate()" x-init="init()" x-on:submit.prevent="submit"
            action="{{ route('admin.banks.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- LEFT COLUMN --}}
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input name="name" required type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('name') }}">
                        @error('name')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" x-model="type" required
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                            <option value="">Select a type</option>
                            @foreach (['Customer Bank', 'Shipper Bank'] as $t)
                                <option value="{{ $t }}" @selected(old('type') === $t)>{{ $t }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Swift Code --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Swift Code
                        </label>
                        <input name="swift_code" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('swift_code') }}">
                        @error('swift_code')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
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

                {{-- MIDDLE COLUMN --}}
                <div class="space-y-4">
                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Phone</label>
                        <input name="phone" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('phone') }}">
                        @error('phone')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Country --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Country</label>
                        <input name="country" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('country') }}">
                        @error('country')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bank Account --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Bank Account</label>
                        <input name="bank_account" type="text"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            value="{{ old('bank_account') }}">
                        @error('bank_account')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Company (type-dependent, searchable dropdown) --}}
                    <div x-id="['company-dropdown']">
                        <label class="block text-sm font-medium mb-1">
                            Company (depends on Type)
                        </label>

                        <div class="relative" @click.away="openCompanyDropdown = false">
                            {{-- visible “select-like” button --}}
                            <button type="button" :aria-disabled="!company_type"
                                :class="company_type ? 'cursor-pointer' : 'cursor-not-allowed opacity-60'"
                                class="w-full flex items-center justify-between rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-left"
                                @click="if (company_type) { openCompanyDropdown = !openCompanyDropdown }">

                                <span class="truncate"
                                    x-text="company_name
                                                || (company_type
                                                    ? 'Select Company'
                                                    : 'Select Type first')">
                                </span>

                                <svg class="w-4 h-4 ml-2 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>

                            {{-- dropdown panel --}}
                            <div x-show="openCompanyDropdown" x-transition
                                class="absolute z-30 mt-1 w-full rounded-2xl shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700"
                                style="display:none">

                                {{-- search input at the top --}}
                                <div class="p-2 border-b border-gray-100 dark:border-gray-700">
                                    <input type="text" x-model="companySearch" placeholder="Search company..."
                                        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5 text-sm" />
                                </div>

                                {{-- option list --}}
                                <ul class="max-h-56 overflow-y-auto py-1 text-sm">
                                    <template x-for="option in filteredCompanies" :key="option.id">
                                        <li>
                                            <button type="button"
                                                class="w-full text-left px-3 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700"
                                                :class="String(option.id) === String(company_id) ?
                                                    'bg-gray-100 dark:bg-gray-700 font-medium' :
                                                    ''"
                                                @click="selectCompany(option)">
                                                <span x-text="option.name"></span>
                                            </button>
                                        </li>
                                    </template>

                                    <li x-show="filteredCompanies.length === 0"
                                        class="px-3 py-2 text-gray-500 dark:text-gray-400">
                                        No companies found.
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- hidden fields that go to backend --}}
                        <input type="hidden" name="company_id" :value="company_id">
                        <input type="hidden" name="company_type" :value="company_type">
                        <input type="hidden" name="company_name" :value="company_name">

                        @error('company_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="space-y-4 md:col-span-1">
                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Address</label>
                        <textarea name="address" rows="4"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Note --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">Note</label>
                        <textarea name="note" rows="4"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">{{ old('note') }}</textarea>
                        @error('note')
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
            function bankCreate() {
                return {
                    // main state
                    type: @json(old('type', '')), // 'Customer Bank' | 'Shipper Bank'
                    company_id: @json(old('company_id')),
                    company_name: @json(old('company_name', '')),
                    companyOptions: [],

                    // dropdown state
                    openCompanyDropdown: false,
                    companySearch: '',

                    get company_type() {
                        if (this.type === 'Customer Bank') return 'customer';
                        if (this.type === 'Shipper Bank') return 'shipper';
                        return null;
                    },

                    get filteredCompanies() {
                        if (!this.companySearch) return this.companyOptions;
                        const term = this.companySearch.toLowerCase();
                        return this.companyOptions.filter(c =>
                            c.name.toLowerCase().includes(term)
                        );
                    },

                    init() {
                        // on type change, reload companies
                        this.$watch('type', () => {
                            this.company_id = '';
                            this.company_name = '';
                            this.companyOptions = [];
                            this.companySearch = '';
                            this.openCompanyDropdown = false;

                            if (this.company_type) {
                                this.fetchCompanies();
                            }
                        });

                        // initial load when coming back with old() values
                        if (this.company_type) {
                            this.fetchCompanies().then(() => {
                                if (this.company_id) {
                                    const found = this.companyOptions.find(
                                        c => String(c.id) === String(this.company_id)
                                    );
                                    if (found) this.company_name = found.name;
                                }
                            });
                        }
                    },

                    async fetchCompanies() {
                        if (!this.company_type) return;
                        try {
                            const url = new URL(@json(route('admin.banks.company-options')));
                            url.searchParams.set('type', this.company_type);
                            const res = await fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                            });

                            if (res.ok) {
                                this.companyOptions = await res.json();
                            } else {
                                this.companyOptions = [];
                            }
                        } catch (e) {
                            this.companyOptions = [];
                        }
                    },

                    selectCompany(option) {
                        this.company_id = option.id;
                        this.company_name = option.name;
                        this.openCompanyDropdown = false;
                    },

                    submit() {
                        const form = document.getElementById('bankCreateForm');
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
                                window.safeToast?.('success', res.message ?? 'Bank created');
                                window.location = @json(route('admin.banks.index'));
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
