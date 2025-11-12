@extends('layouts.app')
@section('title','Create Sales Invoice')

@section('content')
<form method="POST" action="{{ route('admin.sales-invoices.store') }}" x-data="salesCreate()"
      x-init="init({ defaultTermsLC: @js($default_terms_lc), defaultTermsTT: @js($default_terms_tt) })"
      class="space-y-6">
    @csrf
    <input type="hidden" name="invoice_no" value="{{ $invoiceNo }}">

    {{-- Header row with shipper/customer/selects --}}
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
        <div class="flex items-center gap-5">
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="type" value="LC" x-model="type"> <span> L/C Invoice</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="radio" name="type" value="TT" x-model="type"> <span> TT Invoice</span>
            </label>
        </div>
        <div>
            <label class="block text-sm">Currency</label>
            <select name="currency_id" class="rounded-xl border px-3 py-2 min-w-[120px]">
                <option value="">—</option>
                @foreach($currencies as $cur)<option value="{{ $cur->id }}">{{ $cur->code }}</option>@endforeach
            </select>
        </div>
        <div class="ml-auto text-right">
            <div class="text-xs text-gray-500">PROFORMA INVOICE</div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">INVOICE No:</span>
                <span class="border rounded-lg px-4 py-1 bg-gray-50">{{ $invoiceNo }}</span>
            </div>
        </div>
    </div>

    {{-- Bank/Receiver block look-alike (simple header, data comes from shipper/customer cards in preview) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 rounded-xl border p-4 bg-white dark:bg-gray-900">
        <div>
            <div class="font-semibold text-blue-700" x-text="$refs.shipperSel?.selectedOptions[0]?.textContent"></div>
            <div class="text-gray-600 text-sm">Our Bank Address:</div>
            <div class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                <em class="text-gray-500">Will render the shipper’s bank address on preview.</em>
            </div>
        </div>
        <div>
            <div class="text-gray-600 text-sm font-semibold">Purchaser :</div>
            <div class="mt-2 text-sm">
                <em class="text-gray-500">Will render the customer’s address on preview.</em>
            </div>
        </div>
    </div>

    {{-- Meta row --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs text-gray-500">Issue Date</label>
            <input type="date" name="issue_date" class="w-full rounded-xl border px-3 py-2">
        </div>
        <div>
            <label class="block text-xs text-gray-500">Delivery Date</label>
            <input type="date" name="delivery_date" class="w-full rounded-xl border px-3 py-2">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-gray-500">Payment Mode</label>
            <input name="payment_mode" class="w-full rounded-xl border px-3 py-2"
                   :placeholder="type==='LC' ? 'Transferable L/C at sight' : 'Telegraphic Transfer'">
        </div>
        <div>
            <label class="block text-xs text-gray-500">Terms of Shipment</label>
            <input name="terms_of_shipment" class="w-full rounded-xl border px-3 py-2">
        </div>
    </div>

    {{-- Items table --}}
    <div class="overflow-x-auto rounded-xl border bg-white dark:bg-gray-900">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-3 py-2">Art Num</th>
                    <th class="px-3 py-2">ARTICLE DESCRIPTION</th>
                    <th class="px-3 py-2">SIZE</th>
                    <th class="px-3 py-2">HS CODE</th>
                    <th class="px-3 py-2">QTY</th>
                    <th class="px-3 py-2">UNIT PRICE</th>
                </tr>
            </thead>
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

                {{-- Commercial cost row (single per invoice) --}}
                <tr class="border-t bg-gray-50/50">
                    <td class="px-3 py-2" colspan="4">
                        <span class="text-sm text-gray-700">Commercial cost</span>
                    </td>
                    <td class="px-3 py-2" colspan="2">
                        <input type="number" step="0.01" name="commercial_cost" class="border rounded px-2 py-1 w-40" value="0">
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="p-3 flex items-center gap-3">
            <button class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200" type="button" @click="rows.push({})">+ More</button>
            <button class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200" type="button" @click="rows.length && rows.pop()">− Remove</button>
            <div class="ml-auto flex items-center gap-3">
                <span>Siatex Discount</span>
                <input type="number" step="0.01" name="siatex_discount" class="border rounded px-2 py-1 w-40" value="0">
            </div>
        </div>
    </div>

    {{-- Terms --}}
    <div>
        <label class="block text-sm font-medium mb-1">Terms and Condition</label>
        <textarea name="terms_and_conditions" x-model="terms" rows="7"
                  class="w-full border rounded-xl px-3 py-2 whitespace-pre"></textarea>
    </div>

    <div class="flex justify-end">
        <button class="rounded-2xl bg-blue-600 text-white px-6 py-2 hover:bg-blue-700">Create</button>
    </div>
</form>

<script>
function salesCreate(){
    return {
        type: 'LC',
        rows: [{},{},{}],
        terms: '',
        init({defaultTermsLC, defaultTermsTT}) {
            this.$watch('type', (v)=> { this.terms = (v === 'LC' ? defaultTermsLC : defaultTermsTT) });
            this.terms = defaultTermsLC;
        },
    }
}
</script>
@endsection
