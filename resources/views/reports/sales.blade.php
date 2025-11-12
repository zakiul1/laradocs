@extends('layouts.app')
@section('title','Sales Report')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Sales Report</h1>

    {{-- Filters (optional; add your own action URL if needed) --}}
    <form method="get" class="grid grid-cols-1 md:grid-cols-6 gap-3 bg-white dark:bg-gray-900 border rounded-xl p-4">
        <div class="md:col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Shipper</label>
            <select name="shipper_id" class="w-full rounded-lg border px-3 py-2">
                <option value="">All</option>
                @foreach($filters['shippers'] as $s)
                    <option value="{{ $s->id }}" @selected(request('shipper_id')==$s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Customer</label>
            <select name="customer_id" class="w-full rounded-lg border px-3 py-2">
                <option value="">All</option>
                @foreach($filters['customers'] as $c)
                    <option value="{{ $c->id }}" @selected(request('customer_id')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Type</label>
            <select name="type" class="w-full rounded-lg border px-3 py-2">
                <option value="">All</option>
                <option value="LC" @selected(request('type')==='LC')>LC</option>
                <option value="TT" @selected(request('type')==='TT')>TT</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Currency</label>
            <select name="currency_id" class="w-full rounded-lg border px-3 py-2">
                <option value="">All</option>
                @foreach($filters['currencies'] as $cur)
                    <option value="{{ $cur->id }}" @selected(request('currency_id')==$cur->id)>{{ $cur->code }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-6 flex items-end justify-end gap-3">
            <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border px-3 py-2">
            <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border px-3 py-2">
            <button class="rounded-lg bg-gray-100 dark:bg-gray-800 px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-700">Apply</button>
        </div>
    </form>

    <div class="rounded-xl border bg-white dark:bg-gray-900 p-6">
        <div class="text-center text-gray-700 mb-3 font-medium">
            SALES - {{ auth()->user()->company_name ?? 'Siatex (BD) Limited' }}
        </div>
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const data = @json($rows);
const labels = data.map(r => r.fy_label);
const values = data.map(r => Number(r.amount));

const ctx = document.getElementById('salesChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Total ($USD)',
            data: values
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
@endsection
