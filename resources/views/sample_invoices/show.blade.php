@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <header class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Sample Invoice #{{ $invoice->invoice_no }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.sample-invoices.index') }}"
                    class="px-4 py-2 rounded-2xl bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-100">
                    Back
                </a>
                <a href="{{ route('admin.sample-invoices.pdf', $invoice) }}"
                    class="px-4 py-2 rounded-2xl text-white bg-gradient-to-r from-indigo-500 to-blue-600 hover:opacity-90">
                    Download PDF
                </a>
            </div>
        </header>

        @include('sample_invoices._preview_card', ['invoice' => $invoice])
    </div>
@endsection
