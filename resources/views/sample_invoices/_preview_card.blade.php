<div class="rounded-2xl bg-white/90 dark:bg-gray-800 shadow-lg p-6 space-y-4">
    {{-- Header --}}
    <div class="flex justify-between gap-6">
        <div class="w-2/3">
            <div class="text-lg font-semibold mb-1">
                {{ $invoice->shipper_name }}
            </div>
            <div class="text-xs whitespace-pre-line">
                {{ $invoice->shipper_address }}
            </div>
        </div>
        <div class="flex flex-col items-end">
            <span class="text-xs text-gray-500 mb-1">INVOICE No.</span>
            <div
                class="min-w-[120px] text-center border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2 bg-gray-50 dark:bg-gray-900 font-semibold">
                {{ $invoice->invoice_no }}
            </div>
        </div>
    </div>

    {{-- Details + Receiver --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-2 text-sm">
            <h2 class="font-semibold mb-1">Details:</h2>
            <p>Buyer Account: {{ $invoice->buyer_account }}</p>
            <p>Shipment Terms: {{ $invoice->shipment_terms }}</p>
            <p>Courier Name: {{ $invoice->courier_name }}</p>
            <p>Tracking Number: {{ $invoice->tracking_number }}</p>
        </div>

        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-xs whitespace-pre-line">
            <h2 class="font-semibold mb-1">Receiver :</h2>
            {{ $invoice->customer_address_block }}
        </div>
    </div>

    {{-- Items --}}
    <div class="mt-4">
        <table class="w-full text-xs border border-gray-200 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-900">
                <tr>
                    <th class="px-2 py-1 border-b">Art Num</th>
                    <th class="px-2 py-1 border-b">ARTICLE DESCRIPTION</th>
                    <th class="px-2 py-1 border-b">SIZE</th>
                    <th class="px-2 py-1 border-b">HS CODE</th>
                    <th class="px-2 py-1 border-b">QTY</th>
                    <th class="px-2 py-1 border-b">UNIT PRICE</th>
                    <th class="px-2 py-1 border-b">SUB TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td class="border-t px-2 py-1">{{ $item->art_num }}</td>
                        <td class="border-t px-2 py-1">{{ $item->description }}</td>
                        <td class="border-t px-2 py-1">{{ $item->size }}</td>
                        <td class="border-t px-2 py-1">{{ $item->hs_code }}</td>
                        <td class="border-t px-2 py-1 text-right">{{ $item->qty }}</td>
                        <td class="border-t px-2 py-1 text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="border-t px-2 py-1 text-right">{{ number_format($item->sub_total, 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6" class="border-t px-2 py-1 text-right font-semibold">Total :</td>
                    <td class="border-t px-2 py-1 text-right font-semibold">
                        {{ number_format($invoice->total_amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Footer note --}}
    <div class="mt-4 text-xs whitespace-pre-line">
        {{ $invoice->footer_note }}
    </div>
</div>
