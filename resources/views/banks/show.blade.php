@extends('layouts.app')

@section('content')
    @php
        use App\Models\Customer;
        use App\Models\Company;

        // Resolve type label & badge by bank->type (Customer Bank / Shipper Bank)
        $typeBadgeClass = match ($bank->type) {
            'Customer Bank' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
            'Shipper Bank' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        };

        // Resolve related company model (polymorphic)
        $relatedCompany = $bank->company;

        $companyTypeKey = null; // 'customer' | 'shipper' | null
        $companyTypeLabel = null; // 'Customer' | 'Shipper' | null
        $companyDisplayName = null; // final name to show

        if ($bank->company_type === Customer::class) {
            $companyTypeKey = 'customer';
            $companyTypeLabel = 'Customer';
            $companyDisplayName = $relatedCompany->name ?? ($bank->company_name ?? null);
        } elseif ($bank->company_type === Company::class) {
            $companyTypeKey = 'shipper';
            $companyTypeLabel = 'Shipper';
            $companyDisplayName =
                $relatedCompany->company_name ?? ($relatedCompany->name ?? ($bank->company_name ?? null));
        }

        $companyBadgeClass =
            $companyTypeKey === 'customer'
                ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200'
                : ($companyTypeKey === 'shipper'
                    ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200'
                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200');
    @endphp

    <div class="max-w-5xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $bank->name }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.banks.index') }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Back
                </a>
                <a href="{{ route('admin.banks.edit', $bank) }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Edit
                </a>
                <form class="inline" method="POST" action="{{ route('admin.banks.destroy', $bank) }}"
                    onsubmit="return confirm('Delete this bank?')">
                    @csrf
                    @method('DELETE')
                    <button
                        class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                        Delete
                    </button>
                </form>
            </div>
        </header>

        <div class="rounded-2xl p-6 shadow-lg bg-white/90 dark:bg-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                {{-- Type --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Type</label>
                    <p class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-medium {{ $typeBadgeClass }}">
                        {{ $bank->type ?? '—' }}
                    </p>
                </div>

                {{-- Company --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Company</label>
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40 min-h-[40px] flex items-center justify-between gap-2">
                        <span>{{ $companyDisplayName ?? '—' }}</span>
                        @if ($companyDisplayName && $companyTypeLabel)
                            <span class="px-2 py-0.5 rounded-xl text-[11px] {{ $companyBadgeClass }}">
                                {{ $companyTypeLabel }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Swift Code --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Swift Code</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->swift_code ?? '—' }}
                    </p>
                </div>

                {{-- Bank Account --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Bank Account</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->bank_account ?? '—' }}
                    </p>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->email ?? '—' }}
                    </p>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->phone ?? '—' }}
                    </p>
                </div>

                {{-- Country --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Country</label>
                    <p
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/40">
                        {{ $bank->country ?? '—' }}
                    </p>
                </div>

                {{-- Address --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Address</label>
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-white/70 dark:bg-gray-900/40 min-h-[64px]">
                        {!! nl2br(e($bank->address ?? '—')) !!}
                    </div>
                </div>

                {{-- Note --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Note</label>
                    <div
                        class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-white/70 dark:bg-gray-900/40 min-h-[64px]">
                        {!! nl2br(e($bank->note ?? '—')) !!}
                    </div>
                </div>

                {{-- Meta --}}
                <div class="grid grid-cols-2 gap-3 md:col-span-2">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Created</p>
                        <p class="font-medium">{{ $bank->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Updated</p>
                        <p class="font-medium">{{ $bank->updated_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    @if ($bank->creator)
                        <div class="md:col-span-2">
                            <p class="text-gray-500 dark:text-gray-400">Created By</p>
                            <p class="font-medium">{{ $bank->creator->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
