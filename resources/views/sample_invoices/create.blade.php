@extends('layouts.app')
@section('title','Create Sample Invoice')

@section('content')
<form method="POST" action="{{ route('admin.sample-invoices.store') }}" x-data="{rows:[{},{},{}]}"
      class="space-y-6">
    @csrf
    <input type="hidden" name="invoice_no" value="{{ $invoiceNo }}">

    <div class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm">Select Shipper</label>
            <select name="shipper_id" class="rounded-xl border px-3 py-2 min-w-[260px]">
                @foreach($shippers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm">Customer</label>
            <select name="customer_id" class="rounded-xl border px-3 py-2 min-w-[320px]">
                @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm">Currency</label>
            <select name="currency_id" class="rounded-xl border px-3 py-2 min-w-[120px]">
                <option value="">—</option>
                @foreach($currencies as $cur)<option value="{{ $cur->id }}">{{ $cur->code }}</option>@endforeach
            </select>
        </div>
        <div class="ml-auto text-right">
            <div class="text-xs text-gray-500">SAMPLE INVOICE No</div>
            <div class="border rounded-lg px-4 py-1 inline-block mt-1">{{ $invoiceNo }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div><label class="block text-xs text-gray-500">Buyer Account</label>
            <input name="buyer_account" class="w-full rounded-xl border px-3 py-2"></div>
        <div><label class="block text-xs text-gray-500">Shipment Terms</label>
            <select name="shipment_terms" class="w-full rounded-xl border px-3 py-2"><option value="">—</option><option>Collect</option><option>Prepaid</option></select></div>
        <div><label class="block text-xs text-gray-500">Courier Name</label>
            <input name="courier_name" class="w-full rounded-xl border px-3 py-2" placeholder="DHL / Aramex / FedEx / UPS"></div>
        <div class="md:col-span-2"><label class="block text-xs text-gray-500">Tracking Number</label>
            <input name="tracking_number" class="w-full rounded-xl border px-3 py-2"></div>
    </div>

    <div class="overflow-x-auto rounded-xl border bg-white dark:bg-gray-900">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800"><tr>
                <th class="px-3 py-2">Art Num</th>
                <th class="px-3 py-2">ARTICLE DESCRIPTION</th>
                <th class="px-3 py-2">SIZE</th>
                <th class="px-3 py-2">HS CODE</th>
                <th class="px-3 py-2">QTY</th>
                <th class="px-3 py-2">UNIT PRICE</th>
            </tr></thead>
            <tbody>
                <template x-for="(r, i) in rows" :key="i">
                    <tr class="border-t">
                        <td class="px-3 py-1"><input :name="`items[${i}][art_num]`" class="border rounded px-2 py-1"></td>
                        <td class="px-3 py-1"><input :name="`items[${i}][article_description]`" class="border rounded px-2 py-1 w-full"></td>
                        <td class="px-3 py-1"><input :name="`items[${i}][size]`" class="border rounded px-2 py-1"></td>
                        <td class="px-3 py-1"><input :name="`items[${i}][hs_code]`" class="border rounded px-2 py-1 w-28"></td>
                        <td class="px-3 py-1"><input type="number" step="0.001" :name="`items[${i}][qty]`" class="border rounded px-2 py-1 w-28"></td>
                        <td class="px-3 py-1"><input type="number" step="0.0001" :name="`items[${i}][unit_price]`" class="border rounded px-2 py-1 w-32"></td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="p-3 flex items-center gap-3">
            <button class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200" type="button" @click="rows.push({})">+ More</button>
            <button class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200" type="button" @click="rows.length && rows.pop()">− Remove</button>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Footer Note</label>
        <textarea name="footer_note" rows="3" class="w-full border rounded-xl px-3 py-2">1. Sample of no commercial value.
2. Values provided for the Customs Purposes ONLY.</textarea>
    </div>

    <div class="flex justify-end">
        <button class="rounded-2xl bg-blue-600 text-white px-6 py-2 hover:bg-blue-700">Create</button>
    </div>
</form>
@endsection
