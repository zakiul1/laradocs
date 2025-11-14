@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Sales Invoice</h1>
            <a href="{{ route('admin.sales-invoices.create') }}"
                class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                + Add Invoice
            </a>
        </header>

        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium mb-1">Select Shipper</label>
                <select name="shipper_id"
                    class="rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 min-w-[220px]">
                    <option value="">All Shippers</option>
                    @foreach ($shippers as $s)
                        <option value="{{ $s->id }}" @selected(request('shipper_id') == $s->id)>
                            {{ $s->company_name ?? $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Customer</label>
                <select name="customer_id"
                    class="rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2 min-w-[220px]">
                    <option value="">All Customers</option>
                    @foreach ($customers as $c)
                        <option value="{{ $c->id }}" @selected(request('customer_id') == $c->id)>
                            {{ $c->company_name }} ({{ $c->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select name="invoice_type"
                    class="rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2">
                    <option value="">All</option>
                    <option value="LC" @selected(request('invoice_type') === 'LC')>LC</option>
                    <option value="TT" @selected(request('invoice_type') === 'TT')>TT</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Search</label>
                <input type="text" name="q" value="{{ request('q') }}"
                    class="rounded-2xl border border-gray-300 dark:border-gray-700 bg-white/90 dark:bg-gray-800 px-4 py-2"
                    placeholder="Invoice no / shipper / customer">
            </div>

            <button
                class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                Filter
            </button>
        </form>

        <div class="rounded-2xl shadow-lg bg-white/90 dark:bg-gray-800 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/60 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Invoice No</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Shipper</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Invoice Date</th>
                        <th class="px-4 py-3 text-right">Price</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($invoices as $inv)
                        <tr>
                            <td class="px-4 py-2">{{ $inv->id }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.sales-invoices.show', $inv) }}"
                                    class="text-indigo-600 hover:underline">{{ $inv->invoice_no }}</a>
                            </td>
                            <td class="px-4 py-2">{{ $inv->invoice_type }}</td>
                            <td class="px-4 py-2">{{ $inv->shipper_name }}</td>
                            <td class="px-4 py-2">{{ $inv->customer_company_name }}</td>
                            <td class="px-4 py-2">
                                {{ optional($inv->issue_date)->format('jS M Y') }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{ $inv->currency_code }} {{ number_format($inv->grand_total, 2) }}
                            </td>
                            <td class="px-4 py-2 text-right space-x-1">
                                <a href="{{ route('admin.sales-invoices.show', $inv) }}"
                                    class="inline-flex items-center text-xs px-2 py-1 rounded-xl bg-gray-200 dark:bg-gray-700">
                                    View
                                </a>
                                <a href="{{ route('admin.sales-invoices.edit', $inv) }}"
                                    class="inline-flex items-center text-xs px-2 py-1 rounded-xl bg-gray-200 dark:bg-gray-700">
                                    Edit
                                </a>
                                <a href="{{ route('admin.sales-invoices.pdf', $inv) }}"
                                    class="inline-flex items-center text-xs px-2 py-1 rounded-xl bg-gray-200 dark:bg-gray-700">
                                    PDF
                                </a>

                                <form method="POST" action="{{ route('admin.sales-invoices.destroy', $inv) }}"
                                    class="inline" onsubmit="return confirm('Delete this sales invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        class="inline-flex items-center text-xs px-2 py-1 rounded-xl bg-red-500 text-white hover:bg-red-600">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                No sales invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $invoices->links() }}
        </div>
    </div>
@endsection
