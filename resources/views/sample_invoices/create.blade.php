@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6" x-data="sampleInvoiceForm()">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Create Sample Invoice</h1>

            <a href="{{ route('admin.sample-invoices.index') }}"
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                Back
            </a>
        </header>

        <form id="sampleInvoiceForm" method="POST" action="{{ route('admin.sample-invoices.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="invoice_no" :value="invoiceNo">
            <input type="hidden" name="footer_note" :value="footerNote">

            <div class="rounded-2xl bg-white/90 dark:bg-gray-800 shadow-lg p-6 space-y-4">
                {{-- HEADER --}}
                <div class="flex justify-between gap-6">
                    <div class="w-2/3">
                        <select name="shipper_id"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 text-lg font-semibold mb-1"
                            x-on:change="updateShipperText($event)">
                            <option value="">Select Shipper</option>
                            @foreach ($shippers as $s)
                                <option value="{{ $s->id }}"
                                    data-address="{{ trim(preg_replace('/\s+/', ' ', $s->address ?? '')) }}"
                                    @selected($shipperId == $s->id)>
                                    {{ $s->company_name ?? $s->name }}
                                </option>
                            @endforeach
                        </select>

                        <textarea x-ref="shipperBlock" class="w-full mt-1 text-xs leading-tight bg-transparent border-none p-0 resize-none"
                            rows="4" readonly></textarea>
                    </div>

                    <div class="flex flex-col items-end">
                        <span class="text-xs text-gray-500 mb-1">INVOICE No.</span>
                        <div
                            class="min-w-[120px] text-center border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 bg-gray-50 dark:bg-gray-900 font-semibold">
                            <span x-text="invoiceNo"></span>
                        </div>
                    </div>
                </div>

                {{-- DETAILS & RECEIVER --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-3">
                        <h2 class="font-semibold mb-1">Details:</h2>

                        <div class="space-y-1">
                            <label class="text-xs">Buyer Account</label>
                            <input type="text" name="buyer_account"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1 text-sm">
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs">Shipment Terms</label>
                            <select name="shipment_terms"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1 text-sm">
                                <option value="Collect">Collect</option>
                                <option value="Prepaid">Prepaid</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs">Courier Name</label>
                            <select name="courier_company_id"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1 text-sm">
                                <option value="">Select One</option>
                                @foreach ($couriers as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->company_name ?? $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs">Tracking Number</label>
                            <input type="text" name="tracking_number"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1 text-sm">
                        </div>
                    </div>

                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                        <h2 class="font-semibold mb-1">Receiver :</h2>
                        <select name="customer_id"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 px-2 py-1 text-sm mb-2"
                            x-on:change="updateReceiverBlock($event)">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" data-company="{{ $c->company_name }}"
                                    data-address="{{ trim(preg_replace('/\s+/', ' ', $c->shipping_address ?? ($c->address ?? ''))) }}"
                                    data-country="{{ $c->country }}" data-attention="{{ $c->name }}"
                                    @selected($customerId == $c->id)>
                                    {{ $c->company_name }} ({{ $c->name }})
                                </option>
                            @endforeach
                        </select>

                        <textarea x-ref="receiverBlock" class="w-full text-xs leading-tight bg-transparent border-none p-0 resize-none"
                            rows="7" readonly></textarea>
                    </div>
                </div>

                {{-- ITEMS --}}
                <div class="mt-6">
                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden bg-white/90 dark:bg-gray-900">
                        <table class="w-full text-xs md:text-sm">
                            <thead class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-200">
                                <tr>
                                    <th class="px-3 py-2 text-left w-24">Art Num</th>
                                    <th class="px-3 py-2 text-left">Article Description</th>
                                    <th class="px-3 py-2 text-left w-20">Size</th>
                                    <th class="px-3 py-2 text-left w-24">HS Code</th>
                                    <th class="px-3 py-2 text-right w-20">Qty</th>
                                    <th class="px-3 py-2 text-right w-28">Unit Price</th>
                                    <th class="px-3 py-2 text-right w-28">Sub Total</th>
                                    <th class="px-3 py-2 text-center w-6">#</th>
                                </tr>
                            </thead>

                            <tbody>
                                <template x-for="(row, index) in rows" :key="row.key">
                                    <tr class="border-t border-gray-100 dark:border-gray-700">
                                        {{-- Art Num --}}
                                        <td class="px-3 py-2 align-top">
                                            <input type="text"
                                                class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 focus:ring-0 focus:outline-none"
                                                :name="`items[${index}][art_num]`" x-model="row.art_num">
                                        </td>

                                        {{-- Description --}}
                                        <td class="px-3 py-2 align-top">
                                            <textarea
                                                class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 focus:ring-0 focus:outline-none"
                                                rows="2" :name="`items[${index}][description]`" x-model="row.description"></textarea>
                                        </td>

                                        {{-- Size --}}
                                        <td class="px-3 py-2 align-top">
                                            <input type="text"
                                                class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 focus:ring-0 focus:outline-none"
                                                :name="`items[${index}][size]`" x-model="row.size">
                                        </td>

                                        {{-- HS Code --}}
                                        <td class="px-3 py-2 align-top">
                                            <input type="text"
                                                class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 focus:ring-0 focus:outline-none"
                                                :name="`items[${index}][hs_code]`" x-model="row.hs_code">
                                        </td>

                                        {{-- Qty --}}
                                        <td class="px-3 py-2 align-top text-right">
                                            <input type="number" min="0"
                                                class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-right focus:ring-0 focus:outline-none"
                                                :name="`items[${index}][qty]`" x-model.number="row.qty"
                                                x-on:input="recalcRow(row)">
                                        </td>

                                        {{-- Unit Price --}}
                                        <td class="px-3 py-2 align-top text-right">
                                            <input type="number" min="0" step="0.01"
                                                class="w-full px-2 py-1 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-right focus:ring-0 focus:outline-none"
                                                :name="`items[${index}][unit_price]`" x-model.number="row.unit_price"
                                                x-on:input="recalcRow(row)">
                                        </td>

                                        {{-- Sub Total --}}
                                        <td class="px-3 py-2 align-top text-right">
                                            <input type="hidden" :name="`items[${index}][sub_total]`"
                                                :value="row.sub_total">
                                            <span class="inline-block min-w-[70px]"
                                                x-text="row.sub_total.toFixed(2)"></span>
                                        </td>

                                        {{-- Row remove icon (still uses your removeRow) --}}
                                        <td class="px-2 py-2 align-top text-center">
                                            <button type="button" class="text-red-500 hover:text-red-700 text-xs"
                                                x-on:click="removeRow()">
                                                &times;
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>

                            <tfoot class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <td colspan="8" class="px-3 py-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <div class="space-x-2">
                                                <button type="button"
                                                    class="px-3 py-1.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                                                    x-on:click="addRow()">
                                                    + Add Line
                                                </button>
                                                <button type="button"
                                                    class="px-3 py-1.5 rounded-xl bg-red-500 hover:bg-red-600 text-white"
                                                    x-on:click="removeRow()">
                                                    × Remove
                                                </button>
                                            </div>

                                            <div class="text-sm">
                                                <span class="font-semibold mr-2">Total</span>
                                                <span x-text="totalFormatted"></span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>


                {{-- FOOTER NOTE --}}
                <div class="mt-4">
                    <textarea rows="3" class="w-full text-xs border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2"
                        x-model="footerNote"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="submit" name="preview" value="1"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    See Preview
                </button>
                <button type="submit"
                    class="px-6 py-3 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                    Save
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
            function sampleInvoiceForm() {
                return {
                    invoiceNo: @json($nextInvoiceNo),
                    footerNote: "1. Sample of no commercial value.\n2. Values provided for the Customs Purposes ONLY.",
                    rows: [{
                            key: Date.now(),
                            art_num: '',
                            description: '',
                            size: '',
                            hs_code: '',
                            qty: 0,
                            unit_price: 0,
                            sub_total: 0
                        },
                        {
                            key: Date.now() + 1,
                            art_num: '',
                            description: '',
                            size: '',
                            hs_code: '',
                            qty: 0,
                            unit_price: 0,
                            sub_total: 0
                        },
                    ],

                    get total() {
                        return this.rows.reduce((sum, r) => sum + (r.sub_total || 0), 0);
                    },
                    get totalFormatted() {
                        return '$' + this.total.toFixed(2);
                    },

                    addRow() {
                        this.rows.push({
                            key: Date.now() + Math.random(),
                            art_num: '',
                            description: '',
                            size: '',
                            hs_code: '',
                            qty: 0,
                            unit_price: 0,
                            sub_total: 0
                        });
                    },
                    removeRow() {
                        if (this.rows.length > 1) this.rows.pop();
                    },
                    recalcRow(row) {
                        const q = Number(row.qty || 0);
                        const u = Number(row.unit_price || 0);
                        row.sub_total = q * u;
                    },

                    updateShipperText(e) {
                        const opt = e.target.selectedOptions[0];
                        if (!opt) return;
                        const company = opt.textContent.trim();
                        const addr = opt.dataset.address || '';
                        const lines = [company, addr].filter(Boolean);
                        this.$refs.shipperBlock.value = lines.join("\n");
                    },

                    updateReceiverBlock(e) {
                        const o = e.target.selectedOptions[0];
                        if (!o) return;
                        const company = o.dataset.company || '';
                        const addr = o.dataset.address || '';
                        const country = o.dataset.country || '';
                        const att = o.dataset.attention || '';
                        const lines = [
                            company,
                            addr,
                            country,
                            '',
                            att ? 'Attention: ' + att : ''
                        ].filter(Boolean);
                        this.$refs.receiverBlock.value = lines.join("\n");
                    },
                };
            }
        </script>
    @endpush
@endsection
