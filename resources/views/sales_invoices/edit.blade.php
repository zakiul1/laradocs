@extends('layouts.app')

@section('content')
    @php
        // Items for Alpine
        $initialItems = old(
            'items',
            $invoice->items
                ->map(
                    fn($it) => [
                        'art_num' => $it->art_num,
                        'description' => $it->description,
                        'size' => $it->size,
                        'qty' => $it->qty,
                        'unit_price' => $it->unit_price,
                    ],
                )
                ->toArray(),
        );

        if (empty($initialItems)) {
            $initialItems = [['art_num' => '', 'description' => '', 'size' => '', 'qty' => '', 'unit_price' => '']];
        }

        $initialState = [
            'invoice_type' => old('invoice_type', $invoice->invoice_type),
            'issue_date' => old('issue_date', optional($invoice->issue_date)->format('Y-m-d')),
            'delivery_date' => old('delivery_date', optional($invoice->delivery_date)->format('Y-m-d')),
            'payment_mode' => old('payment_mode', $invoice->payment_mode),
            'terms_of_shipment' => old('terms_of_shipment', $invoice->terms_of_shipment),
            'commercial_cost' => old('commercial_cost', $invoice->commercial_cost),
            'siatex_discount' => old('siatex_discount', $invoice->siatex_discount),
            'terms_and_conditions' => old('terms_conditions', $invoice->terms_conditions),

            'message_type' => old('message_type', $invoice->message_type),
            'message_body' => old('message_body', $invoice->message_body),
            'footer_note' => old('footer_note', $invoice->footer_note),
        ];
    @endphp

    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Edit Sales Invoice</h1>
            <a href="{{ route('admin.sales-invoices.index') }}"
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                Back
            </a>
        </header>

        <div x-data="salesInvoiceEditor(
            @json($initialItems),
            @json($initialState), {
                lcPayment: @json($defaultPaymentModeLC),
                ttPayment: @json($defaultPaymentModeTT),
                lcTerms: @json($defaultTermsLC),
                ttTerms: @json($defaultTermsTT),
                fobMessage: @json($defaultFobMessage),
                cifMessage: @json($defaultCifMessage)
            }
        )"
            class="rounded-2xl shadow-lg bg-white/90 dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-700">

            <form id="salesInvoiceEditForm" method="POST" action="{{ route('admin.sales-invoices.update', $invoice) }}">

                @csrf
                @method('PUT')

                {{-- TOP: Shipper + type + no --}}
                <div
                    class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <div class="flex-1 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium mb-1">
                                    Select Shipper <span class="text-red-500">*</span>
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
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Type</label>
                                <div class="flex items-center gap-4 mt-1">
                                    <label class="inline-flex items-center gap-1 text-sm">
                                        <input type="radio" name="invoice_type" value="LC"
                                            x-model="state.invoice_type">
                                        <span>L/C Invoice</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1 text-sm">
                                        <input type="radio" name="invoice_type" value="TT"
                                            x-model="state.invoice_type">
                                        <span>TT Invoice</span>
                                    </label>
                                </div>
                                @error('invoice_type')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Our Bank (Shipper Bank)</label>
                            <select name="bank_id"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                                <option value="">Select Bank</option>
                                @foreach ($banks as $b)
                                    {{-- <== uses $banks --}}
                                    <option value="{{ $b->id }}" @selected(old('bank_id', $invoice->bank_id) == $b->id)>
                                        {{ $b->name }} ({{ $b->bank_account }})
                                    </option>
                                @endforeach
                            </select>

                            @error('bank_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="w-full md:w-64 space-y-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Invoice No</label>
                            <input type="number" name="invoice_no" value="{{ old('invoice_no', $invoice->invoice_no) }}"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                                required>
                            @error('invoice_no')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Currency</label>
                            <select name="currency_id"
                                class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                                <option value="">Select Currency</option>
                                @foreach ($currencies as $cur)
                                    <option value="{{ $cur->id }}" @selected(old('currency_id', $invoice->currency_id) == $cur->id)>
                                        {{ $cur->code }}
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_id')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- MIDDLE CARDS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/80 dark:bg-gray-800/80">
                        <h2 class="text-sm font-semibold mb-1">Our Bank Address</h2>
                        <div
                            class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/60 min-h-[96px] text-sm whitespace-pre-line">
                            {{ $invoice->bank_snapshot }}
                        </div>
                    </div>

                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/80 dark:bg-gray-800/80 space-y-3">
                        <h2 class="text-sm font-semibold mb-1">Purchaser</h2>

                        <div>
                            <label class="block text-xs font-medium mb-1">Customer <span
                                    class="text-red-500">*</span></label>
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
                            <label class="block text-xs font-medium mb-1">Purchaser Snapshot</label>
                            <div
                                class="rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/60 min-h-[96px] text-sm whitespace-pre-line">
                                {{ $invoice->customer_address_block }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ISSUE / DELIVERY / PAYMENT / TERMS --}}
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4 text-sm">
                    <div>
                        <label class="block text-xs font-medium mb-1">Issue Date</label>
                        <input type="date" name="issue_date" x-model="state.issue_date"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-3 py-1.5">
                        @error('issue_date')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1">Delivery Date</label>
                        <input type="date" name="delivery_date" x-model="state.delivery_date"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-3 py-1.5">
                        @error('delivery_date')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium mb-1">Payment Mode</label>
                        <input type="text" name="payment_mode" x-model="state.payment_mode"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-3 py-1.5">
                        @error('payment_mode')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1">Terms of Shipment</label>
                        <input type="text" name="terms_of_shipment" x-model="state.terms_of_shipment"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-3 py-1.5">
                        @error('terms_of_shipment')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ITEMS TABLE (same as create) --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden mb-4">
                    <table class="min-w-full text-xs md:text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                            <tr>
                                <th class="px-2 py-2 text-left w-24">Art Num</th>
                                <th class="px-2 py-2 text-left">Article Description</th>
                                <th class="px-2 py-2 text-left w-24">Size</th>
                                <th class="px-2 py-2 text-right w-20">Qty</th>
                                <th class="px-2 py-2 text-right w-28">Unit Price</th>
                                <th class="px-2 py-2 text-right w-28">Sub Total</th>
                                <th class="px-2 py-2 text-center w-10">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in items" :key="index">
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    <td class="px-2 py-1.5 align-top">
                                        <input type="text"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model="row.art_num" :name="'items[' + index + '][art_num]'">
                                    </td>
                                    <td class="px-2 py-1.5 align-top">
                                        <textarea rows="2"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model="row.description" :name="'items[' + index + '][description]'"></textarea>
                                    </td>
                                    <td class="px-2 py-1.5 align-top">
                                        <input type="text"
                                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model="row.size" :name="'items[' + index + '][size]'">
                                    </td>
                                    <td class="px-2 py-1.5 align-top text-right">
                                        <input type="number" min="0" step="1"
                                            class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model.number="row.qty" :name="'items[' + index + '][qty]'">
                                    </td>
                                    <td class="px-2 py-1.5 align-top text-right">
                                        <input type="number" min="0" step="0.01"
                                            class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                            x-model.number="row.unit_price" :name="'items[' + index + '][unit_price]'">
                                    </td>
                                    <td class="px-2 py-1.5 align-top text-right">
                                        <span class="inline-block min-w-[60px]"
                                            x-text="formatMoney(lineTotal(row))"></span>
                                    </td>
                                    <td class="px-2 py-1.5 align-top text-center">
                                        <button type="button" class="text-red-500 hover:text-red-700 text-xs"
                                            @click="removeRow(index)" x-show="items.length > 1">
                                            &times;
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            {{-- Add line --}}
                            <tr class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                                <td colspan="7" class="px-2 py-2">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                                        @click="addRow">
                                        + Add Line
                                    </button>
                                </td>
                            </tr>

                            {{-- Commercial cost --}}
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td colspan="4"></td>
                                <td class="px-2 py-2 text-right font-medium">
                                    Commercial cost
                                </td>
                                <td class="px-2 py-2 text-right">
                                    <input type="number" min="0" step="0.01"
                                        class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                        x-model.number="state.commercial_cost" name="commercial_cost">
                                </td>
                                <td></td>
                            </tr>

                            {{-- Items total + qty --}}
                            <tr class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                                <td colspan="3"></td>
                                <td class="px-2 py-2 text-right font-semibold">
                                    <span x-text="totalQty() + ' Pcs'"></span>
                                </td>
                                <td class="px-2 py-2 text-right font-semibold">
                                    Items Total
                                </td>
                                <td class="px-2 py-2 text-right font-semibold">
                                    <span x-text="formatMoney(itemsTotalWithCommercial())"></span>
                                </td>
                                <td></td>
                            </tr>

                            {{-- Discount --}}
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td colspan="4"></td>
                                <td class="px-2 py-2 text-right font-medium">
                                    Siatex Discount
                                </td>
                                <td class="px-2 py-2 text-right">
                                    <input type="number" min="0" step="0.01"
                                        class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                        x-model.number="state.siatex_discount" name="siatex_discount">
                                </td>
                                <td></td>
                            </tr>

                            {{-- Grand total --}}
                            <tr class="border-t border-gray-100 dark:border-gray-700 bg-gray-100 dark:bg-gray-800">
                                <td colspan="4"></td>
                                <td class="px-2 py-2 text-right font-semibold">
                                    Total
                                </td>
                                <td class="px-2 py-2 text-right font-semibold">
                                    <span x-text="formatMoney(grandTotal())"></span>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Terms & conditions --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Terms and Conditions</label>
                    <textarea name="terms_conditions" rows="5"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-sm"
                        x-model="state.terms_and_conditions"></textarea>
                    @error('terms_and_conditions')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- FOB / CIF message --}}
                <div class="mb-4 space-y-2">
                    <div class="flex items-center gap-4 text-sm">
                        <label class="inline-flex items-center gap-1">
                            <input type="radio" name="message_type" value="FOB" x-model="state.message_type">
                            <span>FOB Message</span>
                        </label>
                        <label class="inline-flex items-center gap-1">
                            <input type="radio" name="message_type" value="CIF" x-model="state.message_type">
                            <span>CIF Message</span>
                        </label>
                    </div>

                    <textarea name="message_body" rows="3"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-sm"
                        x-model="state.message_body"></textarea>
                    @error('message_body')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Footer note --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Footer Note (bottom line)</label>
                    <input type="text" name="footer_note" x-model="state.footer_note"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-sm">
                    @error('footer_note')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
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
    {{-- Reuse same Alpine helper as in create --}}
    <script>
        function salesInvoiceEditor(initialItems, initialState, defaults) {
            return {
                items: initialItems || [],
                state: initialState || {},
                defaults: defaults || {},

                init() {
                    if (!this.items.length) this.addRow();
                    this.applyTypeDefaultsIfEmpty();
                    this.applyMessageDefaultsIfEmpty();

                    this.$watch('state.invoice_type', () => this.applyTypeDefaultsIfEmpty());
                    this.$watch('state.message_type', () => this.applyMessageDefaultsIfEmpty());
                },

                addRow() {
                    this.items.push({
                        art_num: '',
                        description: '',
                        size: '',
                        qty: '',
                        unit_price: ''
                    });
                },
                removeRow(i) {
                    if (this.items.length > 1) this.items.splice(i, 1);
                },

                lineTotal(row) {
                    return Number(row.qty || 0) * Number(row.unit_price || 0);
                },
                itemsTotal() {
                    return this.items.reduce((s, r) => s + this.lineTotal(r), 0);
                },
                totalQty() {
                    return this.items.reduce((s, r) => s + Number(r.qty || 0), 0);
                },
                itemsTotalWithCommercial() {
                    return this.itemsTotal() + Number(this.state.commercial_cost || 0);
                },
                grandTotal() {
                    return this.itemsTotalWithCommercial() - Number(this.state.siatex_discount || 0);
                },
                formatMoney(v) {
                    return Number(v || 0).toFixed(2);
                },

                applyTypeDefaultsIfEmpty() {
                    if (this.state.invoice_type === 'LC') {
                        if (!this.state.payment_mode) this.state.payment_mode = this.defaults.lcPayment || '';
                        if (!this.state.terms_and_conditions) this.state.terms_and_conditions = this.defaults.lcTerms || '';
                    } else if (this.state.invoice_type === 'TT') {
                        if (!this.state.payment_mode) this.state.payment_mode = this.defaults.ttPayment || '';
                        if (!this.state.terms_and_conditions) this.state.terms_and_conditions = this.defaults.ttTerms || '';
                    }
                },
                applyMessageDefaultsIfEmpty() {
                    if (this.state.message_type === 'FOB') {
                        if (!this.state.message_body) this.state.message_body = this.defaults.fobMessage || '';
                    } else if (this.state.message_type === 'CIF') {
                        if (!this.state.message_body) this.state.message_body = this.defaults.cifMessage || '';
                    }
                },

                submitForm() {
                    document.getElementById('salesInvoiceEditForm').submit();
                },
            };
        }
    </script>
@endpush
