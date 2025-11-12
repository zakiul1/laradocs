@extends('layouts.app')
@section('title','Sample Invoice')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Sample Invoice</h1>
        <a href="{{ route('admin.sample-invoices.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-blue-600 text-white px-4 py-2 hover:bg-blue-700">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"/></svg>
            Add Invoice
        </a>
    </div>

    <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-white dark:bg-gray-900 border rounded-xl p-4">
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
            <input name="search" value="{{ request('search') }}" class="w-full rounded-lg border px-3 py-2">
        </div>
        <div class="md:col-span-4 flex justify-end">
            <button class="rounded-lg bg-gray-100 dark:bg-gray-800 px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-700">Apply</button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border bg-white dark:bg-gray-900">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
            <tr class="text-left">
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Sample Invoice No</th>
                <th class="px-4 py-2">Shipper</th>
                <th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Currency</th>
                <th class="px-4 py-2 text-right">Total Price</th>
                <th class="px-4 py-2">Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($invoices as $inv)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $inv->id }}</td>
                    <td class="px-4 py-2">{{ $inv->invoice_no }}</td>
                    <td class="px-4 py-2">{{ $inv->shipper->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $inv->customer->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $inv->currency->code ?? '' }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($inv->grand_total,2) }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.sample-invoices.show',$inv) }}">View</a>
                            <a class="text-amber-600 hover:underline" href="{{ route('admin.sample-invoices.edit',$inv) }}">Edit</a>
                            <a class="text-green-700 hover:underline" href="{{ route('admin.sample-invoices.pdf',$inv) }}">PDF</a>
                            <form method="post" action="{{ route('admin.sample-invoices.destroy',$inv) }}" onsubmit="return confirm('Delete this invoice?')">
                                @csrf @method('delete')
                                <button class="text-red-600 hover:underline" type="submit">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No invoices</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $invoices->links() }}</div>
</div>
@endsection
