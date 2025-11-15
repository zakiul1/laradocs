{{-- resources/views/sales_invoices/show.blade.php --}}
@extends('layouts.app')

@section('content')
    @php
        // convenience fallbacks so this view works with slightly different column names
        $bankBlock = $invoice->bank_snapshot ?? ($invoice->our_bank_block ?? '');
        $purchaserBlock = $invoice->purchaser_snapshot ?? ($invoice->customer_address_block ?? '');
        $termsText = $invoice->terms_conditions ?? ($invoice->terms_and_conditions ?? '');

        // ----- NORMALIZE ITEMS TO A SIMPLE ARRAY -----
        $rawItems = $invoice->items ?? [];
        if ($rawItems instanceof \Illuminate\Support\Collection) {
            $items = $rawItems->all();
        } elseif (is_array($rawItems)) {
            $items = $rawItems;
        } else {
            $items = [];
        }

        // helpers to read from array OR object
        $get = function ($row, $key, $default = null) {
            if (is_array($row)) {
                return $row[$key] ?? $default;
            }
            if (is_object($row)) {
                return $row->{$key} ?? $default;
            }
            return $default;
        };

        // ----- TOTALS -----
        $itemsTotal =
            $invoice->items_total ??
            array_reduce(
                $items,
                function ($sum, $row) use ($get) {
                    $qty = (float) $get($row, 'qty', 0);
                    $unit = (float) $get($row, 'unit_price', 0);
                    return $sum + $qty * $unit;
                },
                0,
            );

        $commercialCost = $invoice->commercial_cost ?? 0;
        $siatexDiscount = $invoice->siatex_discount ?? 0;

        $grandTotal =
            $invoice->grand_total ?? ($invoice->total_amount ?? $itemsTotal + $commercialCost - $siatexDiscount);

        $totalQty =
            $invoice->total_qty ??
            array_reduce(
                $items,
                function ($sum, $row) use ($get) {
                    return $sum + (float) $get($row, 'qty', 0);
                },
                0,
            );
    @endphp

    <div class="max-w-5xl mx-auto p-6 space-y-6">
        {{-- HEADER --}}
        <header class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">
                    Sales Invoice
                    @if (!empty($invoice->invoice_no))
                        <span class="text-gray-500 text-base font-normal">#{{ $invoice->invoice_no }}</span>
                    @endif
                </h1>

                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                    @if (!empty($invoice->invoice_type))
                        <span
                            class="inline-flex items-center rounded-full bg-indigo-100 text-indigo-700 px-3 py-1 font-semibold uppercase tracking-wide">
                            {{ $invoice->invoice_type }} Invoice
                        </span>
                    @endif

                    @isset($preview)
                        @if ($preview)
                            <span
                                class="inline-flex items-center rounded-full bg-yellow-100 text-yellow-800 px-3 py-1 font-semibold uppercase tracking-wide">
                                Preview (Not Saved)
                            </span>
                        @endif
                    @endisset
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.sales-invoices.index') }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100 text-sm">
                    Back to list
                </a>

                @empty($preview)
                    @if ($invoice->id)
                        <a href="{{ route('admin.sales-invoices.edit', $invoice) }}"
                            class="px-4 py-2 rounded-2xl bg-blue-500 hover:bg-blue-600 text-white text-sm">
                            Edit
                        </a>
                        <a href="{{ route('admin.sales-invoices.pdf', $invoice) }}"
                            class="px-4 py-2 rounded-2xl bg-gray-800 hover:bg-black text-white text-sm" target="_blank">
                            Download PDF
                        </a>
                    @endif
                @endempty
            </div>
        </header>

        {{-- TOP INFO BLOCKS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Our Bank Address --}}
            <div
                class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/80 dark:bg-gray-800/80 space-y-2">
                <h2 class="text-sm font-semibold mb-1">Our Bank Address</h2>
                <div class="text-sm whitespace-pre-line text-gray-800 dark:text-gray-100">
                    {{ $bankBlock }}
                </div>
            </div>

            {{-- Purchaser --}}
            <div
                class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/80 dark:bg-gray-800/80 space-y-2">
                <h2 class="text-sm font-semibold mb-1">Purchaser</h2>
                <div class="text-sm whitespace-pre-line text-gray-800 dark:text-gray-100">
                    {{ $purchaserBlock }}
                </div>
            </div>
        </div>

        {{-- META DATA ROW --}}
        <div
            class="grid grid-cols-1 md:grid-cols-5 gap-3 border border-gray-200 dark:border-gray-700 rounded-2xl p-4 text-xs md:text-sm bg-white/80 dark:bg-gray-900/70">
            <div>
                <div class="font-medium text-gray-600 dark:text-gray-300">Issue Date</div>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ optional($invoice->issue_date)->format('jS M Y') }}
                </div>
            </div>
            <div>
                <div class="font-medium text-gray-600 dark:text-gray-300">Delivery Date</div>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ optional($invoice->delivery_date)->format('jS M Y') ?: '—' }}
                </div>
            </div>
            <div>
                <div class="font-medium text-gray-600 dark:text-gray-300">Payment Mode</div>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ $invoice->payment_mode ?? '—' }}
                </div>
            </div>
            <div>
                <div class="font-medium text-gray-600 dark:text-gray-300">Terms of Shipment</div>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ $invoice->terms_of_shipment ?? '—' }}
                </div>
            </div>
            <div>
                <div class="font-medium text-gray-600 dark:text-gray-300">Currency</div>
                <div class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ $invoice->currency_code ?? (optional($invoice->currency)->code ?? '—') }}
                </div>
            </div>
        </div>

        {{-- ITEMS TABLE --}}
        <div class="border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden bg-white/90 dark:bg-gray-900">
            <table class="min-w-full text-xs md:text-sm">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left w-24">Art Num</th>
                        <th class="px-3 py-2 text-left">Article Description</th>
                        <th class="px-3 py-2 text-left w-24">Size</th>
                        <th class="px-3 py-2 text-right w-20">Qty</th>
                        <th class="px-3 py-2 text-right w-28">Unit Price</th>
                        <th class="px-3 py-2 text-right w-28">Sub Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($items as $row)
                        <tr>
                            <td class="px-3 py-2 align-top">
                                {{ $get($row, 'art_num', '') }}
                            </td>
                            <td class="px-3 py-2 align-top whitespace-pre-line">
                                {{ $get($row, 'description', '') }}
                            </td>
                            <td class="px-3 py-2 align-top">
                                {{ $get($row, 'size', '') }}
                            </td>
                            <td class="px-3 py-2 align-top text-right">
                                {{ number_format($get($row, 'qty', 0)) }}
                            </td>
                            <td class="px-3 py-2 align-top text-right">
                                {{ number_format($get($row, 'unit_price', 0), 2) }}
                            </td>
                            <td class="px-3 py-2 align-top text-right">
                                @php
                                    $qty = (float) $get($row, 'qty', 0);
                                    $unit = (float) $get($row, 'unit_price', 0);
                                @endphp
                                {{ number_format($get($row, 'sub_total', $qty * $unit), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                                No items found for this invoice.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="bg-gray-50 dark:bg-gray-800">
                    {{-- Commercial Cost --}}
                    <tr class="border-t border-gray-100 dark:border-gray-700">
                        <td colspan="4"></td>
                        <td class="px-3 py-2 text-right text-xs md:text-sm">Commercial Cost</td>
                        <td class="px-3 py-2 text-right">
                            {{ number_format($commercialCost, 2) }}
                        </td>
                    </tr>

                    {{-- Total Qty & Items Total --}}
                    <tr class="border-t border-gray-100 dark:border-gray-700">
                        <td colspan="3"></td>
                        <td class="px-3 py-2 text-right font-semibold">Total Qty:</td>
                        <td class="px-3 py-2 text-right">
                            {{ number_format($totalQty) }} Pcs
                        </td>
                        <td class="px-3 py-2 text-right font-semibold">
                            {{ number_format($itemsTotal, 2) }}
                        </td>
                    </tr>

                    {{-- Siatex Discount --}}
                    <tr>
                        <td colspan="4"></td>
                        <td class="px-3 py-2 text-right text-xs md:text-sm">Siatex Discount</td>
                        <td class="px-3 py-2 text-right">
                            {{ number_format($siatexDiscount, 2) }}
                        </td>
                    </tr>

                    {{-- Grand Total --}}
                    <tr>
                        <td colspan="4"></td>
                        <td class="px-3 py-3 text-right font-semibold">Total:</td>
                        <td class="px-3 py-3 text-right font-semibold">
                            {{ $invoice->currency_code ?? (optional($invoice->currency)->code ?? '') }}
                            {{ number_format($grandTotal, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- TERMS & CONDITIONS --}}
        <div class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/90 dark:bg-gray-900">
            <h2 class="text-sm font-semibold mb-2">Terms and Conditions</h2>
            <div class="text-sm whitespace-pre-line text-gray-800 dark:text-gray-100">
                {{ $termsText }}
            </div>
        </div>

        {{-- MESSAGE + FOOTER --}}
        <div class="space-y-4">
            <div class="border border-gray-200 dark:border-gray-700 rounded-2xl p-4 bg-white/90 dark:bg-gray-900">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-sm font-semibold">Message</h2>
                    @if (!empty($invoice->message_type))
                        <span
                            class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 px-3 py-1 text-xs font-semibold">
                            {{ $invoice->message_type }} Terms
                        </span>
                    @endif
                </div>
                <div class="text-sm whitespace-pre-line text-gray-800 dark:text-gray-100">
                    {{ $invoice->message_body }}
                </div>
            </div>

            <div
                class="border border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-3 bg-white/70 dark:bg-gray-900/60 text-center text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-100">
                {{ $invoice->footer_note }}
            </div>
        </div>
    </div>
@endsection
