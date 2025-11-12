@extends('layouts.app')
@section('title','Preview Sales Invoice')

@section('content')
<div class="space-y-5 print:p-0">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Preview Sales Invoice</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales-invoices.create') }}" class="px-3 py-2 rounded bg-blue-50 text-blue-700">+ Sample Invoice Add</a>
            <a href="{{ route('admin.sales-invoices.edit',$invoice) }}" class="px-3 py-2 rounded bg-blue-600 text-white">Edit</a>
            <a href="{{ route('admin.sales-invoices.pdf',$invoice) }}" class="px-3 py-2 rounded bg-emerald-600 text-white">Download PDF</a>
        </div>
    </div>

    <div class="rounded-xl border bg-white dark:bg-gray-900 p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <div class="text-lg font-semibold text-gray-800 dark:text-white">{{ $invoice->shipper->name ?? '-' }}</div>
                <div class="text-sm text-gray-600">
                    {{-- Put shipper address/bank lines here if you store them on shipper --}}
                    Our Bank Address:
                    <div class="mt-2 whitespace-pre-line text-gray-700">City Bank limited,
Account # 1401820958001,
Gulshan Branch , Dhaka,
Bangladesh</div>
                </div>
            </div>
            <div>
                <div class="flex items-start justify-between">
                    <h3 class="text-gray-600 font-semibold">Purchaser :</h3>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">PROFORMA INVOICE</div>
                        <div class="border rounded-lg px-4 py-1 inline-block mt-1">{{ $invoice->invoice_no }}</div>
                    </div>
                </div>
                <div class="mt-2 whitespace-pre-line text-gray-700">
                    {{ $invoice->customer->name ?? '-' }}
                    {{-- Customer address line(s) if stored --}}
                </div>
            </div>
        </div>

        {{-- Meta row --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-6 text-sm">
            <div><div class="text-gray-500">ISSUE DATE</div><div class="border rounded px-3 py-1">{{ optional($invoice->issue_date)->toDateString() }}</div></div>
            <div><div class="text-gray-500">DELIVERY DATE</div><div class="border rounded px-3 py-1">{{ optional($invoice->delivery_date)->toDateString() }}</div></div>
            <div class="md:col-span-2"><div class="text-gray-500">PAYMENT MODE</div><div class="border rounded px-3 py-1">{{ $invoice->payment_mode }}</div></div>
            <div><div class="text-gray-500">CURRENCY</div><div class="border rounded px-3 py-1">{{ $invoice->currency->code ?? '' }}</div></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2 text-sm">
            <div class="md:col-span-2"><div class="text-gray-500">TERMS OF SHIPMENT</div><div class="border rounded px-3 py-1">{{ $invoice->terms_of_shipment }}</div></div>
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

                    {{-- Commercial cost + totals --}}
                    <tr class="border-t">
                        <td class="px-3 py-2" colspan="6" align="right">Items Total :</td>
                        <td class="px-3 py-2 text-right">{{ number_format($invoice->items_total,2) }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-3 py-2" colspan="6" align="right">Commercial cost :</td>
                        <td class="px-3 py-2 text-right">{{ number_format($invoice->commercial_cost,2) }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-3 py-2" colspan="6" align="right">Siatex Discount :</td>
                        <td class="px-3 py-2 text-right">-{{ number_format($invoice->siatex_discount,2) }}</td>
                    </tr>
                    <tr class="border-t font-semibold">
                        <td class="px-3 py-2" colspan="6" align="right">Total :</td>
                        <td class="px-3 py-2 text-right">{{ number_format($invoice->grand_total,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Terms --}}
        <div class="mt-6">
            <div class="font-semibold">Terms and Condition {{ $invoice->type==='LC' ? '(required in the L/C)' : '' }}:</div>
            <div class="mt-2 whitespace-pre-line text-sm">{{ $invoice->terms_and_conditions }}</div>
        </div>
    </div>
</div>
@endsection
