@extends('layouts.app')

@section('content')
    @php
        // Prepare initial items for Alpine (old() first, then DB items)
        $initialItems = old(
            'items',
            $invoice->items
                ->map(function ($it) {
                    return [
                        'art_num' => $it->art_num,
                        'description' => $it->description,
                        'size' => $it->size,
                        'hs_code' => $it->hs_code,
                        'qty' => $it->qty,
                        'unit_price' => $it->unit_price,
                    ];
                })
                ->toArray(),
        );

        // Fallback: at least one empty row if no items
        if (empty($initialItems)) {
            $initialItems = [
                [
                    'art_num' => '',
                    'description' => '',
                    'size' => '',
                    'hs_code' => '',
                    'qty' => '',
                    'unit_price' => '',
                ],
            ];
        }
    @endphp

    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Sample Invoice</h1>
            <a href="{{ route('admin.sample-invoices.index') }}"
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                Back
            </a>
        </header>

        {{-- A4-ish paper feel --}}
        <div x-data='sampleInvoiceEditor(@json($initialItems))'
            class="rounded-2xl shadow-lg bg-white/90 dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-700">

            <form id="sampleInvoiceEditForm" method="POST" action="{{ route('admin.sample-invoices.update', $invoice) }}"
                @submit.prevent="submitForm">
                @csrf
                @method('PUT')

                {{-- TOP: Shipper + Invoice No --}}
                <div
                    class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <div class="space-y-2 flex-1">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Shipper <span class="text-red-500">*</span>
                            </label>
                            <select name="shipper_id"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                required>
                                <option value="">Select Shipper</option>
                                @foreach ($shippers as $s)
                                    <option value="{{ $s->id }}" @selected(old('shipper_id', $invoice->shipper_id) == $s->id)>
                                        {{ $s->company_name ?? $s->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shipper_id')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Shipper Address (snapshot)</label>
                            <div
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-800/60 min-h-[56px] text-sm">
                                {!! nl2br(e($invoice->shipper_address)) !!}
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-64 space-y-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Sample Invoice No</label>
                            <input type="number" name="invoice_no" value="{{ old('invoice_no', $invoice->invoice_no) }}"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                required>
                            @error('invoice_no')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Buyer A/C</label>
                            <input type="text" name="buyer_account"
                                value="{{ old('buyer_account', $invoice->buyer_account) }}"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                            @error('buyer_account')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- MIDDLE CARDS: Details & Receiver --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Details --}}
                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/80 dark:bg-gray-800/80 space-y-3">
                        <h2 class="text-sm font-semibold mb-1">Details</h2>

                        {{-- Shipment Terms --}}
                        <div>
                            <label class="block text-xs font-medium mb-1">
                                Shipment Terms <span class="text-red-500">*</span>
                            </label>
                            <select name="shipment_terms"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5 text-sm"
                                required>
                                <option value="">Select</option>
                                @foreach (['Collect', 'Prepaid'] as $term)
                                    <option value="{{ $term }}" @selected(old('shipment_terms', $invoice->shipment_terms) === $term)>
                                        {{ $term }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shipment_terms')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Courier --}}
                        <div>
                            <label class="block text-xs font-medium mb-1">Courier Name</label>
                            <select name="courier_company_id"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5 text-sm">
                                <option value="">Select Courier</option>
                                @foreach ($couriers as $co)
                                    <option value="{{ $co->id }}" @selected(old('courier_company_id', $invoice->courier_company_id) == $co->id)>
                                        {{ $co->company_name ?? $co->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('courier_company_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tracking No --}}
                        <div>
                            <label class="block text-xs font-medium mb-1">Tracking No</label>
                            <input type="text" name="tracking_number"
                                value="{{ old('tracking_number', $invoice->tracking_number) }}"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5 text-sm">
                            @error('tracking_number')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Receiver --}}
                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/80 dark:bg-gray-800/80 space-y-3">
                        <h2 class="text-sm font-semibold mb-1">Receiver</h2>

                        <div>
                            <label class="block text-xs font-medium mb-1">
                                Customer <span class="text-red-500">*</span>
                            </label>
                            <select name="customer_id"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5 text-sm"
                                required>
                                <option value="">Select Customer</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" @selected(old('customer_id', $invoice->customer_id) == $c->id)>
                                        {{ $c->company_name }} ({{ $c->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium mb-1">Receiver Snapshot</label>
                            <div
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/60 min-h-[72px] text-sm">
                                {!! nl2br(e($invoice->customer_address_block)) !!}
                            </div>
                            <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                Address &amp; country are stored as snapshot when invoice is saved.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ITEMS TABLE --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden mb-4">
                    <table class="min-w-full text-xs md:text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                            <tr>
                                <th class="px-2 py-2 text-left w-28">Art Num</th>
                                <th class="px-2 py-2 text-left">Article Description</th>
                                <th class="px-2 py-2 text-left w-24">Size</th>
                                <th class="px-2 py-2 text-left w-28">HS Code</th>
                                <th class="px-2 py-2 text-right w-20">Qty</th>
                                <th class="px-2 py-2 text-right w-28">Unit Price</th>
                                <th class="px-2 py-2 text-right w-28">Sub Total</th>
                                <th class="px-2 py-2 text-center w-10">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in items" :key="index">
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    {{-- Art Num --}}
                                    <td class="px-2 py-1.5 align-top">
                                        <input type="text"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model="row.art_num" :name="'items[' + index + '][art_num]'">
                                    </td>

                                    {{-- Description --}}
                                    <td class="px-2 py-1.5 align-top">
                                        <textarea rows="2"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model="row.description" :name="'items[' + index + '][description]'"></textarea>
                                    </td>

                                    {{-- Size --}}
                                    <td class="px-2 py-1.5 align-top">
                                        <input type="text"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model="row.size" :name="'items[' + index + '][size]'">
                                    </td>

                                    {{-- HS Code --}}
                                    <td class="px-2 py-1.5 align-top">
                                        <input type="text"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model="row.hs_code" :name="'items[' + index + '][hs_code]'">
                                    </td>

                                    {{-- Qty --}}
                                    <td class="px-2 py-1.5 align-top text-right">
                                        <input type="number" min="0" step="1"
                                            class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model.number="row.qty" :name="'items[' + index + '][qty]'">
                                    </td>

                                    {{-- Unit Price --}}
                                    <td class="px-2 py-1.5 align-top text-right">
                                        <input type="number" min="0" step="0.01"
                                            class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model.number="row.unit_price" :name="'items[' + index + '][unit_price]'">
                                    </td>

                                    {{-- Sub Total (computed) --}}
                                    <td class="px-2 py-1.5 align-top text-right">
                                        <span class="inline-block min-w-[60px]"
                                            x-text="formatMoney(lineTotal(row))"></span>
                                    </td>

                                    {{-- Remove --}}
                                    <td class="px-2 py-1.5 align-top text-center">
                                        <button type="button" class="text-red-500 hover:text-red-700 text-xs"
                                            @click="removeRow(index)" x-show="items.length > 1">
                                            &times;
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <td colspan="8" class="px-2 py-2">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                                        @click="addRow">
                                        + Add Line
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td class="px-2 py-2 text-right font-semibold">Total</td>
                                <td class="px-2 py-2 text-right font-semibold">
                                    <span x-text="formatMoney(grandTotal())"></span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Footer note --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Footer Note</label>
                    <textarea name="footer_note" rows="3"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-sm"
                        placeholder="1. Sample of no commercial value.&#10;2. Values provided for the Customs Purposes ONLY.">{{ old('footer_note', $invoice->footer_note) }}</textarea>
                    @error('footer_note')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ACTIONS --}}
                <div class="flex flex-wrap justify-end gap-3">
                    <button type="submit" name="preview" value="1"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                        Preview
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                        Update Invoice
                    </button>
                </div>

                {{-- Validation summary --}}
                @if ($errors->any())
                    <div class="mt-4 rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-200 p-4">
                        <ul class="list-disc pl-6 text-sm">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function sampleInvoiceEditor(initialItems) {
            return {
                items: Array.isArray(initialItems) && initialItems.length ?
                    initialItems : [{
                        art_num: '',
                        description: '',
                        size: '',
                        hs_code: '',
                        qty: '',
                        unit_price: '',
                    }],

                addRow() {
                    this.items.push({
                        art_num: '',
                        description: '',
                        size: '',
                        hs_code: '',
                        qty: '',
                        unit_price: '',
                    });
                },

                removeRow(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                lineTotal(row) {
                    const q = Number(row.qty || 0);
                    const u = Number(row.unit_price || 0);
                    return q * u;
                },

                grandTotal() {
                    return this.items.reduce((sum, row) => sum + this.lineTotal(row), 0);
                },

                formatMoney(value) {
                    return Number(value || 0).toFixed(2);
                },

                submitForm() {
                    document.getElementById('sampleInvoiceEditForm').submit();
                },
            };
        }
    </script>
@endpush
