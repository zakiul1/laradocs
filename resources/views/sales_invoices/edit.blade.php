@extends('layouts.app')
@section('title','Edit Sales Invoice')

@section('content')
<form method="POST" action="{{ route('admin.sales-invoices.update',$invoice) }}" 
      x-data="salesEdit()" x-init='init(@js($invoice), @js($invoice->items))' class="space-y-6">
    @csrf @method('put')

    <div class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm">Select Shipper</label>
            <select name="shipper_id" class="rounded-xl border px-3 py-2 min-w-[260px]">
                @foreach($shippers as $s)<option value="{{ $s->id }}" @selected($s->id==$invoice->shipper_id)>{{ $s->name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm">Customer</label>
            <select name="customer_id" class="rounded-xl border px-3 py-2 min-w-[320px]">
                @foreach($customers as $c)<option value="{{ $c->id }}" @selected($c->id==$invoice->customer_id)>{{ $c->name }}</option>@endforeach
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
                @foreach($currencies as $cur)<option value="{{ $cur->id }}" @selected($cur->id==$invoice->currency_id)>{{ $cur->code }}</option>@endforeach
            </select>
        </div>
        <div class="ml-auto text-right">
            <div class="text-xs text-gray-500">PROFORMA INVOICE</div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">INVOICE No:</span>
                <span class="border rounded-lg px-4 py-1 bg-gray-50">{{ $invoice->invoice_no }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div><label class="block text-xs text-gray-500">Issue Date</label>
            <input type="date" name="issue_date" value="{{ optional($invoice->issue_date)->toDateString() }}" class="w-full rounded-xl border px-3 py-2"></div>
        <div><label class="block text-xs text-gray-500">Delivery Date</label>
            <input type="date" name="delivery_date" value="{{ optional($invoice->delivery_date)->toDateString() }}" class="w-full rounded-xl border px-3 py-2"></div>
        <div class="md:col-span-2"><label class="block text-xs text-gray-500">Payment Mode</label>
            <input name="payment_mode" value="{{ $invoice->payment_mode }}" class="w-full rounded-xl border px-3 py-2"></div>
        <div><label class="block text-xs text-gray-500">Terms of Shipment</label>
            <input name="terms_of_shipment" value="{{ $invoice->terms_of_shipment }}" class="w-full rounded-xl border px-3 py-2"></div>
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
                <th class="px-3 py-2">—</th>
            </tr></thead>
            <tbody>
                <template x-for="(r, i) in rows" :key="r.key">
                    <tr class="border-t">
                        <td class="px-3 py-1"><input :name="`items[${i}][art_num]`" x-model="r.art_num" class="border rounded px-2 py-1"><input type="hidden" :name="`items[${i}][id]`" :value="r.id"></td>
                        <td class="px-3 py-1"><input :name="`items[${i}][article_description]`" x-model="r.article_description" class="border rounded px-2 py-1 w-full"></td>
                        <td class="px-3 py-1"><input :name="`items[${i}][size]`" x-model="r.size" class="border rounded px-2 py-1"></td>
                        <td class="px-3 py-1"><input :name="`items[${i}][hs_code]`" x-model="r.hs_code" class="border rounded px-2 py-1 w-28"></td>
                        <td class="px-3 py-1"><input type="number" step="0.001" :name="`items[${i}][qty]`" x-model="r.qty" class="border rounded px-2 py-1 w-28"></td>
                        <td class="px-3 py-1"><input type="number" step="0.0001" :name="`items[${i}][unit_price]`" x-model="r.unit_price" class="border rounded px-2 py-1 w-32"></td>
                        <td class="px-3 py-1 text-right"><button type="button" class="text-red-600" @click="toggleDelete(i)" x-text="r._delete ? 'Undo' : 'Remove'"></button>
                            <input type="hidden" :name="`items[${i}][delete]`" :value="r._delete ? 1 : 0">
                        </td>
                    </tr>
                </template>

                <tr class="border-t bg-gray-50/50">
                    <td class="px-3 py-2" colspan="4"><span class="text-sm text-gray-700">Commercial cost</span></td>
                    <td class="px-3 py-2" colspan="3">
                        <input type="number" step="0.01" name="commercial_cost" class="border rounded px-2 py-1 w-40" value="{{ $invoice->commercial_cost }}">
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="p-3 flex items-center gap-3">
            <button class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200" type="button" @click="add()">+ More</button>
            <div class="ml-auto flex items-center gap-3">
                <span>Siatex Discount</span>
                <input type="number" step="0.01" name="siatex_discount" class="border rounded px-2 py-1 w-40" value="{{ $invoice->siatex_discount }}">
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Terms and Condition</label>
        <textarea name="terms_and_conditions" rows="7" class="w-full border rounded-xl px-3 py-2">{{ $invoice->terms_and_conditions }}</textarea>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.sales-invoices.show',$invoice) }}" class="px-4 py-2 rounded-2xl bg-gray-100">Preview</a>
        <button class="rounded-2xl bg-blue-600 text-white px-6 py-2 hover:bg-blue-700">Save</button>
    </div>
</form>

<script>
function salesEdit(){
    return {
        type: 'LC',
        rows: [],
        init(invoice, items) {
            this.type = invoice.type;
            this.rows = (items || []).map((it, idx)=>({...it, key: idx, _delete:false}));
            if(this.rows.length===0) this.rows=[{key:0},{key:1},{key:2}];
        },
        add(){ this.rows.push({key: Date.now()}); },
        toggleDelete(i){ this.rows[i]._delete = !this.rows[i]._delete; }
    }
}
</script>
@endsection
