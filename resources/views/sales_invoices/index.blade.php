@extends('layouts.app')
@section('title','Sales Invoice')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Sales Invoice</h1>
        <a href="{{ route('admin.sales-invoices.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/></svg>
            Add Invoice
        </a>
    </div>

    {{-- Filters --}}
    <form method="get" class="grid grid-cols-1 md:grid-cols-5 gap-3 bg-white dark:bg-gray-900 border rounded-xl p-4">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Type</label>
            <select name="type" class="w-full rounded-lg border px-3 py-2">
                <option value="">All</option>
                <option value="LC" @selected(request('type')==='LC')>LC</option>
                <option value="TT" @selected(request('type')==='TT')>TT</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Shipper</label>
            <select name="shipper_id" class="w-full rounded-lg border px-3 py-2">
                <option value="">All</option>
                @foreach($shippers as $s)
                    <option value="{{ $s->id }}" @selected(request('shipper_id')==$s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Customer</label>
            <select name="customer_id" class="w-full rounded-lg border px-3 py-2">
                <option value="">All</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" @selected(request('customer_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Find something?</label>
            <input name="search" value="{{ request('search') }}" placeholder="Search by invoice / shipper / customer"
                   class="w-full rounded-lg border px-3 py-2">
        </div>
        <div class="md:col-span-5 flex justify-end">
            <button class="rounded-lg bg-gray-100 dark:bg-gray-800 px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-700">Apply</button>
        </div>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl border bg-white dark:bg-gray-900">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr class="text-left">
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Invoice No</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Shipper</th>
                <th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Invoice Date</th>
                <th class="px-4 py-2 text-right">Price</th>
                <th class="px-4 py-2">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($invoices as $inv)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $inv->id }}</td>
                    <td class="px-4 py-2">{{ $inv->invoice_no }}</td>
                    <td class="px-4 py-2">{{ $inv->type }}</td>
                    <td class="px-4 py-2">{{ $inv->shipper->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $inv->customer->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ optional($inv->issue_date)->format('jS M Y') }}</td>
                    <td class="px-4 py-2 text-right">
                        {{ $inv->currency->symbol ?? '' }}{{ number_format($inv->grand_total,2) }}
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.sales-invoices.show',$inv) }}" title="Preview">View</a>
                            <a class="text-amber-600 hover:underline" href="{{ route('admin.sales-invoices.edit',$inv) }}" title="Edit">Edit</a>
                            <a class="text-green-700 hover:underline" href="{{ route('admin.sales-invoices.pdf',$inv) }}" title="PDF">PDF</a>
                            <form method="post" action="{{ route('admin.sales-invoices.destroy',$inv) }}" onsubmit="return confirm('Delete this invoice?')">
                                @csrf @method('delete')
                                <button class="text-red-600 hover:underline" type="submit" title="Delete">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No invoices</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>
</div>
@endsection
