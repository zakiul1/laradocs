@extends('layouts.app')

@section('content')
    @php
        // ----- DEFAULT TEXT BLOCKS (from your screenshots) -----
        $defaultTerms =
            "1. The type of the L/C is irrevocable and transferable at sight.\n" .
            "2. This L/C should be transferred by Uttara Bank Ltd.\n" .
            "3. Trans-shipments and partial shipments are allowed.\n" .
            "4. +/- 3% in quantity and value is accepted.\n" .
            "5. Negotiations are allowed with any Bank in Bangladesh.\n" .
            "6. All discrepancies will be acceptable, except late shipment, prices and quantities.\n" .
            '7. Country of origin: Bangladesh.';

        $defaultFobMessage =
            "Please keep the following words in the B/L or Airway bill terms in the L/C:\n" .
            "\"Full set clean on board ocean bill of lading / airway bill made out to the order of the negotiating bank in Bangladesh and endorsed to the L/C opening bank marked freight Collect.\"";

        $defaultCifMessage =
            "Please keep the following words in the B/L or Airway bill terms in the L/C:\n" . 'H.S Code:61.05.1000';

        $defaultFooterLc = 'PLEASE ADVISED THE L/C THROUGH OUR BANK AS ABOVE';
        $defaultFooterTt = 'PLEASE ADVISED THE TT THROUGH OUR BANK AS ABOVE';

        // ----- INITIAL ITEMS (at least one row) -----
        $initialItems = old('items', [
            [
                'art_num' => '',
                'description' => '',
                'size' => '',
                'qty' => '',
                'unit_price' => '',
            ],
        ]);

        if (empty($initialItems)) {
            $initialItems = [
                [
                    'art_num' => '',
                    'description' => '',
                    'size' => '',
                    'qty' => '',
                    'unit_price' => '',
                ],
            ];
        }

        // ----- STATE FOR ALPINE -----
        $invoiceType = old('invoice_type', 'LC');

        $state = [
            'invoice_type' => $invoiceType, // 'LC' or 'TT'
            'shipper_id' => old('shipper_id'),
            'customer_id' => old('customer_id'),
            'issue_date' => old('issue_date', now()->toDateString()),
            'delivery_date' => old('delivery_date'),
            'payment_mode' => old(
                'payment_mode',
                $invoiceType === 'TT' ? 'Telegraphic Transfer' : 'Transferable L/C at sight',
            ),
            'terms_of_shipment' => old('terms_of_shipment'),
            'currency_id' => old('currency_id'),
            'commercial_cost' => (float) old('commercial_cost', 0),
            'siatex_discount' => (float) old('siatex_discount', 0),
            'terms_conditions' => old('terms_conditions', $defaultTerms),
            'message_type' => old('message_type', 'FOB'), // 'FOB' or 'CIF'
            'fob_message_default' => $defaultFobMessage,
            'cif_message_default' => $defaultCifMessage,
            'message_body' => old('message_body', $defaultFobMessage),
            'footer_note' => old('footer_note', $invoiceType === 'TT' ? $defaultFooterTt : $defaultFooterLc),
            'items' => $initialItems,

            // textareas (editable snapshots)
            'bank_snapshot' => old('bank_snapshot'),
            'purchaser_snapshot' => old('purchaser_snapshot'),

            // pre-calculated blocks from controller (keyed by id)
            'bank_blocks' => $bankBlocks ?? [],
            'customer_blocks' => $customerBlocks ?? [],
        ];
    @endphp

    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Create Sales Invoice</h1>
            <a href="{{ route('admin.sales-invoices.index') }}"
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                Back
            </a>
        </header>

        <div x-data="salesInvoiceEditor(@js($state))" x-init="init()"
            class="rounded-2xl shadow-lg bg-white/90 dark:bg-gray-900 p-6 border border-gray-200 dark:border-gray-700">
            <form id="salesInvoiceCreateForm" method="POST" action="{{ route('admin.sales-invoices.store') }}">

                @csrf

                {{-- TOP BAR: Shipper + Invoice Type + Invoice No --}}
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">

                    <!-- Shipper -->
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium mb-1">Select Shipper <span
                                class="text-red-500">*</span></label>
                        <select name="shipper_id" x-model="shipper_id" @change="updateBankSnapshot()"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            required>
                            <option value="">Select Shipper</option>
                            @foreach ($shippers as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->company_name ?? $s->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('shipper_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Customer Type -->
                    <div>
                        <label class="block text-xs font-medium mb-1">Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" x-model="customer_id" @change="updatePurchaserSnapshot()"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5 text-sm"
                            required>
                            <option value="">Select Customer</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->company_name }} ({{ $c->name }})
                                </option>
                            @endforeach
                        </select>

                        @error('customer_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Invoice Type -->
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium mb-1">Invoice Type</label>
                        <div class="flex items-center gap-4 text-sm">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="invoice_type" value="LC" x-model="invoice_type"
                                    @change="onTypeChanged" class="rounded border-gray-300">
                                <span>L/C Invoice</span>
                            </label>

                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="invoice_type" value="TT" x-model="invoice_type"
                                    @change="onTypeChanged" class="rounded border-gray-300">
                                <span>TT Invoice</span>
                            </label>
                        </div>
                    </div>

                    <!-- Invoice No -->
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium mb-1">Invoice No</label>
                        <input type="number" name="invoice_no" value="{{ old('invoice_no', $nextInvoiceNo) }}"
                            class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                            required>

                        @error('invoice_no')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>


                {{-- HEADER BODY: Our Bank Address + Purchaser --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Our Bank Address (auto from shipper, but editable) --}}
                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/80 dark:bg-gray-800/80 space-y-3">
                        <h2 class="text-sm font-semibold mb-1">Our Bank Address</h2>
                        <textarea name="bank_snapshot" rows="5" x-model="bank_snapshot"
                            class="w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/60 text-sm whitespace-pre-line"></textarea>
                        <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                            This block is taken from the Shipper’s Bank (type = Shipper Bank) automatically,
                            but you can edit it.
                        </p>
                    </div>


                    {{-- Purchaser --}}
                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/80 dark:bg-gray-800/80 space-y-3">
                        <h2 class="text-sm font-semibold mb-1">Purchaser</h2>

                        <div>
                            <textarea name="purchaser_snapshot" rows="5" x-model="purchaser_snapshot"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-2 bg-white/70 dark:bg-gray-900/60 text-sm whitespace-pre-line"></textarea>
                            <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                Company, address &amp; country are auto-filled from the customer but can be edited.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- DATES / PAYMENT / TERMS / CURRENCY --}}
                <div
                    class="grid grid-cols-1 md:grid-cols-5 gap-3 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 mb-4 text-xs md:text-sm">
                    <div>
                        <label class="block font-medium mb-1">Issue Date</label>
                        <input type="date" name="issue_date" x-model="issue_date"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5">
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Delivery Date</label>
                        <input type="date" name="delivery_date" x-model="delivery_date"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5">
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Payment Mode</label>
                        <input type="text" name="payment_mode" x-model="payment_mode"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5">
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Terms of Shipment</label>
                        <input type="text" name="terms_of_shipment" x-model="terms_of_shipment"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5">
                    </div>
                    <div>
                        <label class="block font-medium mb-1">Currency</label>
                        <select name="currency_id" x-model="currency_id"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-3 py-1.5">
                            <option value="">Select</option>
                            @foreach ($currencies as $cur)
                                <option value="{{ $cur->id }}">
                                    {{ $cur->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ITEMS TABLE --}}
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
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800">

                            <tr>
                                <td colspan="7" class="px-2 py-2">
                                    <button type="button"
                                        class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100"
                                        @click="addRow">
                                        + Add Line
                                    </button>
                                </td>
                            </tr>

                            <!-- 1) Commercial Cost -->
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td colspan="4"></td>
                                <td class="px-2 py-1.5 text-right text-xs md:text-sm">Commercial Cost</td>
                                <td class="px-2 py-1.5 text-right">
                                    <input type="number" min="0" step="0.01"
                                        class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                        name="commercial_cost" x-model.number="commercial_cost">
                                </td>
                                <td></td>
                            </tr>

                            <!-- 2) Total Qty -->
                            <tr class="border-t border-gray-100 dark:border-gray-700">
                                <td colspan="3"></td>
                                <td class="px-2 py-1.5 text-right font-semibold">Total Qty:</td>
                                <td class="px-2 py-1.5 text-right" x-text="totalQty() + ' Pcs'"></td>
                                <td class="px-2 py-1.5 text-right font-semibold" x-text="formatMoney(itemsTotal())"></td>
                                <td></td>
                            </tr>

                            <!-- 3) Siatex Discount -->
                            <tr>
                                <td colspan="4"></td>
                                <td class="px-2 py-1.5 text-right text-xs md:text-sm">Siatex Discount</td>
                                <td class="px-2 py-1.5 text-right">
                                    <input type="number" min="0" step="0.01"
                                        class="w-full text-right rounded-lg border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-900 px-2 py-1 text-xs"
                                        name="siatex_discount" x-model.number="siatex_discount">
                                </td>
                                <td></td>
                            </tr>

                            <!-- 4) Final Total -->
                            <tr>
                                <td colspan="4"></td>
                                <td class="px-2 py-2 text-right font-semibold">Total:</td>
                                <td class="px-2 py-2 text-right font-semibold" x-text="formatMoney(grandTotal())"></td>
                                <td></td>
                            </tr>

                        </tfoot>


                    </table>
                </div>

                {{-- Terms & Conditions --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Terms and Conditions</label>
                    <textarea name="terms_conditions" rows="6" x-model="terms_conditions"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-sm whitespace-pre-line"></textarea>
                    @error('terms_conditions')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- FOB / CIF MESSAGE + FOOTER NOTE --}}
                <div class="mb-4 space-y-3">
                    <div class="flex items-center gap-6 text-sm">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="message_type" value="FOB" x-model="message_type"
                                @change="onMessageTypeChanged" class="rounded border-gray-300">
                            <span>FOB Message</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="message_type" value="CIF" x-model="message_type"
                                @change="onMessageTypeChanged" class="rounded border-gray-300">
                            <span>CIF Message</span>
                        </label>
                    </div>

                    <textarea name="message_body" rows="4" x-model="message_body"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-sm whitespace-pre-line"></textarea>
                    @error('message_body')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror

                    <input type="text" name="footer_note" x-model="footer_note"
                        class="w-full rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 text-center text-sm font-semibold uppercase tracking-wide">
                    @error('footer_note')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap justify-end gap-3">
                    <!-- PREVIEW BUTTON -->
                    <button type="submit" formaction="{{ route('admin.sales-invoices.preview', $invoice->id) }}"
                        formmethod="POST"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                        @csrf
                        Preview
                    </button>

                    <!-- SAVE BUTTON -->
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                        Save Invoice
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
        function salesInvoiceEditor(initial) {
            return {
                // primitive fields
                invoice_type: initial.invoice_type || 'LC',
                shipper_id: initial.shipper_id || '',
                customer_id: initial.customer_id || '',
                issue_date: initial.issue_date || '',
                delivery_date: initial.delivery_date || '',
                payment_mode: initial.payment_mode || '',
                terms_of_shipment: initial.terms_of_shipment || '',
                currency_id: initial.currency_id || '',
                commercial_cost: Number(initial.commercial_cost || 0),
                siatex_discount: Number(initial.siatex_discount || 0),
                terms_conditions: initial.terms_conditions || '',
                message_type: initial.message_type || 'FOB',
                fob_message_default: initial.fob_message_default || '',
                cif_message_default: initial.cif_message_default || '',
                message_body: initial.message_body || '',
                footer_note: initial.footer_note || '',

                // editable snapshots (these are posted to backend)
                bank_snapshot: initial.bank_snapshot || '',
                purchaser_snapshot: initial.purchaser_snapshot || '',

                // lookup maps coming from controller
                bankBlocks: initial.bank_blocks || {}, // { shipper_id: "bank text..." }
                customerBlocks: initial.customer_blocks || {}, // { customer_id: "purchaser text..." }

                // items
                items: initial.items || [],

                init() {
                    // defaults
                    if (!this.payment_mode) {
                        this.setDefaultPaymentMode();
                    }
                    if (!this.footer_note) {
                        this.setFooterByType();
                    }
                    if (!this.message_body) {
                        this.message_body = this.message_type === 'CIF' ?
                            this.cif_message_default :
                            this.fob_message_default;
                    }

                    // initial auto-fill from current shipper/customer
                    this.updateBankSnapshot();
                    this.updatePurchaserSnapshot();

                    // react on changes (Alpine 3)
                    if (this.$watch) {
                        this.$watch('shipper_id', () => this.updateBankSnapshot());
                        this.$watch('customer_id', () => this.updatePurchaserSnapshot());
                        this.$watch('invoice_type', () => {
                            this.setDefaultPaymentMode();
                            this.setFooterByType();
                        });
                    }
                },

                // ---------- SNAPSHOT HELPERS ----------
                updateBankSnapshot() {
                    console.log(
                        'shipper_id changed to:', this.shipper_id,
                        ' bankBlocks entry =', this.bankBlocks[this.shipper_id]
                    );

                    if (this.bankBlocks[this.shipper_id]) {
                        this.bank_snapshot = this.bankBlocks[this.shipper_id];
                    } else if (!this.bank_snapshot) {
                        this.bank_snapshot = '';
                    }
                },


                updatePurchaserSnapshot() {
                    if (this.customerBlocks[this.customer_id]) {
                        this.purchaser_snapshot = this.customerBlocks[this.customer_id];
                    } else if (!this.purchaser_snapshot) {
                        this.purchaser_snapshot = '';
                    }
                },

                // ---------- ITEM HELPERS ----------
                addRow() {
                    this.items.push({
                        art_num: '',
                        description: '',
                        size: '',
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

                itemsTotal() {
                    return this.items.reduce((sum, row) => sum + this.lineTotal(row), 0);
                },

                totalQty() {
                    return this.items.reduce((sum, row) => sum + Number(row.qty || 0), 0);
                },

                grandTotal() {
                    return this.itemsTotal() +
                        Number(this.commercial_cost || 0) -
                        Number(this.siatex_discount || 0);
                },

                formatMoney(value) {
                    return Number(value || 0).toFixed(2);
                },

                // ---------- TYPE / PAYMENT / FOOTER ----------
                setDefaultPaymentMode() {
                    this.payment_mode = (this.invoice_type === 'TT') ?
                        'Telegraphic Transfer' :
                        'Transferable L/C at sight';
                },

                setFooterByType() {
                    this.footer_note = (this.invoice_type === 'TT') ?
                        'PLEASE ADVISED THE TT THROUGH OUR BANK AS ABOVE' :
                        'PLEASE ADVISED THE L/C THROUGH OUR BANK AS ABOVE';
                },

                onTypeChanged() {
                    this.setDefaultPaymentMode();
                    this.setFooterByType();
                },

                // ---------- FOB / CIF ----------
                onMessageTypeChanged() {
                    if (this.message_type === 'CIF') {
                        this.message_body = this.cif_message_default;
                    } else {
                        this.message_body = this.fob_message_default;
                    }
                },

                // ---------- SUBMIT ----------
                submitForm() {
                    document.getElementById('salesInvoiceCreateForm').submit();
                }
            };
        }
    </script>
@endpush
