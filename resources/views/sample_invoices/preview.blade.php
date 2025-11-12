@extends('layouts.app')
@section('title','Preview Sample Invoice')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Preview Sample Invoice</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sample-invoices.edit',$invoice) }}" class="px-3 py-2 rounded bg-blue-600 text-white">Edit</a>
            <a href="{{ route('admin.sample-invoices.pdf',$invoice) }}" class="px-3 py-2 rounded bg-emerald-600 text-white">Download PDF</a>
        </div>
    </div>

    <div class="rounded-xl border bg-white dark:bg-gray-900 p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <div class="text-lg font-semibold text-gray-800 dark:text-white">{{ $invoice->shipper->name ?? '-' }}</div>
                <div class="text-sm text-gray-700 mt-2 whitespace-pre-line">Road #06, House #08,
NIketon, Gulshan-1 Dhaka,
Bangladesh</div>
                <div class="mt-4">
                    <div class="text-gray-600 font-semibold">Our Bank Address:</div>
                    <div class="mt-1 text-sm whitespace-pre-line">City Bank limited,
Account # 1401820958001,
Gulshan Branch , Dhaka,
Bangladesh</div>
                </div>
            </div>
            <div>
                <div class="flex items-start justify-between">
                    <h3 class="text-gray-600 font-semibold">Receiver :</h3>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">SAMPLE INVOICE No</div>
                        <div class="border rounded-lg px-4 py-1 inline-block mt-1">{{ $invoice->invoice_no }}</div>
                    </div>
                </div>
                <div class="mt-2 whitespace-pre-line text-sm">
                    {{-- Customer address --}}
                    {{ $invoice->customer->name ?? '-' }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-6 text-sm">
            <div><div class="text-gray-500">Buyer Account</div><div class="border rounded px-3 py-1">{{ $invoice->buyer_account }}</div></div>
            <div><div class="text-gray-500">Shipment Terms</div><div class="border rounded px-3 py-1">{{ $invoice->shipment_terms }}</div></div>
            <div><div class="text-gray-500">Courier Name</div><div class="border rounded px-3 py-1">{{ $invoice->courier_name }}</div></div>
            <div><div class="text-gray-500">Tracking Number</div><div class="border rounded px-3 py-1">{{ $invoice->tracking_number }}</div></div>
        </div>

        {{-- Items --}}
        <div class="overflow-x-auto mt-6">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Art Num</th>
                        <th class="px-3 py-2 text-left">ARTICLE DESCRIPTION</th>
                        <th class="px-3 py-2 text-left">SIZE</th>
                        <th class="px-3 py-2 text-left">HS CODE</th>
                        <th class="px-3 py-2 text-right">QTY</th>
                        <th class="px-3 py-2 text-right">UNIT PRICE</th>
                        <th class="px-3 py-2 text-right">SUB TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $row)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $row->art_num }}</td>
                        <td class="px-3 py-2">{{ $row->article_description }}</td>
                        <td class="px-3 py-2">{{ $row->size }}</td>
                        <td class="px-3 py-2">{{ $row->hs_code }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->qty,3) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->unit_price,4) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($row->sub_total,2) }}</td>
                    </tr>
                    @endforeach

                    <tr class="border-t font-semibold">
                        <td class="px-3 py-2" colspan="6" align="right">Total :</td>
                        <td class="px-3 py-2 text-right">{{ number_format($invoice->grand_total,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 whitespace-pre-line text-sm">
            {{ $invoice->footer_note }}
        </div>
    </div>
</div>
@endsection
