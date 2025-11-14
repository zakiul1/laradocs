@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">
                Sales Invoice #{{ $invoice->invoice_no }}
            </h1>

            <div class="flex gap-2">
                <a href="{{ route('admin.sales-invoices.index') }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Back
                </a>

                <a href="{{ route('admin.sales-invoices.edit', $invoice) }}"
                    class="px-4 py-2 rounded-2xl bg-blue-500 hover:bg-blue-600 text-white">
                    Edit
                </a>

                <a href="{{ route('admin.sales-invoices.pdf', $invoice) }}"
                    class="px-4 py-2 rounded-2xl bg-gray-800 hover:bg-black text-white">
                    Download PDF
                </a>
            </div>
        </header>

        <div class="bg-slate-100 dark:bg-gray-800/80 p-4 rounded-2xl overflow-x-auto">
            @include('sales_invoices._preview_card', ['invoice' => $invoice])
        </div>
    </div>
@endsection
