<?php

namespace App\Http\Controllers;

use App\Models\SampleInvoice;
use App\Models\SampleInvoiceItem;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;


class SampleInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $shipperId = $request->get('shipper_id');
        $customerId = $request->get('customer_id');
        $q = trim((string) $request->get('q'));

        $invoices = SampleInvoice::query()
            ->when($shipperId, fn($qr) => $qr->where('shipper_id', $shipperId))
            ->when($customerId, fn($qr) => $qr->where('customer_id', $customerId))
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($w) use ($q) {
                    $w->where('invoice_no', 'like', "%{$q}%")
                        ->orWhere('shipper_name', 'like', "%{$q}%")
                        ->orWhere('customer_company_name', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('invoice_no')
            ->paginate(15)
            ->withQueryString();

        // Shippers = Company where category 'Shipper'
        $shippers = Company::query()
            ->whereHas('category', function ($q) {
                $q->where('name', 'Shipper')->orWhere('slug', 'shipper');
            })
            ->orderBy('company_name')
            ->get();

        $customers = Customer::orderBy('company_name')->orderBy('name')->get();

        return view('sample_invoices.index', compact('invoices', 'shippers', 'customers'));
    }

    public function create(Request $request)
    {
        $shipperId = $request->get('shipper_id');
        $customerId = $request->get('customer_id');

        $shippers = Company::query()
            ->whereHas('category', function ($q) {
                $q->where('name', 'Shipper')->orWhere('slug', 'shipper');
            })
            ->orderBy('company_name')
            ->get();

        // Couriers = Company where category 'Courier Service'
        $couriers = Company::query()
            ->whereHas('category', function ($q) {
                $q->where('name', 'Courier Service')->orWhere('slug', 'courier-service');
            })
            ->orderBy('company_name')
            ->get();

        $customers = Customer::orderBy('company_name')->orderBy('name')->get();

        // Next invoice number
        $nextInvoiceNo = (int) (SampleInvoice::max('invoice_no') ?? 101000) + 1;

        return view('sample_invoices.create', compact(
            'shippers',
            'customers',
            'couriers',
            'shipperId',
            'customerId',
            'nextInvoiceNo'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request); // create mode

        DB::beginTransaction();
        try {
            $invoice = SampleInvoice::create($data['header']);

            foreach ($data['items'] as $item) {
                $item['sample_invoice_id'] = $invoice->id;
                SampleInvoiceItem::create($item);
            }

            // total already set in header, but keep this for safety
            $invoice->update(['total_amount' => $data['header']['total_amount']]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if ($request->has('preview')) {
            return redirect()->route('admin.sample-invoices.show', $invoice);
        }

        return redirect()
            ->route('admin.sample-invoices.index')
            ->with('success', 'Sample Invoice created.');
    }

    /**
     * Show the form for editing an existing sample invoice.
     */
    public function edit(SampleInvoice $sampleInvoice)
    {
        $sampleInvoice->load('items');

        // Shippers = Company where category 'Shipper'
        $shippers = Company::query()
            ->whereHas('category', function ($q) {
                $q->where('name', 'Shipper')->orWhere('slug', 'shipper');
            })
            ->orderBy('company_name')
            ->get();

        // Couriers = Company where category 'Courier Service'
        $couriers = Company::query()
            ->whereHas('category', function ($q) {
                $q->where('name', 'Courier Service')->orWhere('slug', 'courier-service');
            })
            ->orderBy('company_name')
            ->get();

        $customers = Customer::orderBy('company_name')->orderBy('name')->get();

        return view('sample_invoices.edit', [
            'invoice' => $sampleInvoice,
            'shippers' => $shippers,
            'customers' => $customers,
            'couriers' => $couriers,
        ]);
    }

    /**
     * Update an existing sample invoice.
     */
    public function update(Request $request, SampleInvoice $sampleInvoice)
    {
        $data = $this->validated($request, $sampleInvoice); // update mode

        DB::beginTransaction();
        try {
            // Update header (do not touch created_by)
            $header = $data['header'];
            unset($header['created_by']); // make sure we don't override it accidentally
            $sampleInvoice->update($header);

            // Replace items: simple + safe
            $sampleInvoice->items()->delete();

            foreach ($data['items'] as $item) {
                $item['sample_invoice_id'] = $sampleInvoice->id;
                SampleInvoiceItem::create($item);
            }

            $sampleInvoice->update(['total_amount' => $data['header']['total_amount']]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if ($request->has('preview')) {
            return redirect()->route('admin.sample-invoices.show', $sampleInvoice);
        }

        return redirect()
            ->route('admin.sample-invoices.index')
            ->with('success', 'Sample Invoice updated.');
    }

    public function show(SampleInvoice $sampleInvoice)
    {
        $sampleInvoice->load('items');

        return view('sample_invoices.show', [
            'invoice' => $sampleInvoice,
        ]);
    }
    public function destroy(SampleInvoice $sampleInvoice)
    {
        // Make sure items are deleted first (FK constraints)
        DB::transaction(function () use ($sampleInvoice) {
            $sampleInvoice->items()->delete();
            $sampleInvoice->delete();
        });

        return redirect()
            ->route('admin.sample-invoices.index')
            ->with('success', 'Sample Invoice deleted successfully.');
    }

    public function pdf(SampleInvoice $sampleInvoice)
    {
        $sampleInvoice->load('items');

        $pdf = PDF::loadView('sample_invoices.pdf', ['invoice' => $sampleInvoice])
            ->setPaper('a4');

        $filename = 'sample-invoice-' . $sampleInvoice->invoice_no . '.pdf';

        return $pdf->download($filename);
    }

    // ---------------- helpers ----------------

    /**
     * Shared validation for create & update.
     *
     * When $invoice is null => create mode.
     * When $invoice is provided => update mode (ignore its own invoice_no in unique rule).
     */
    protected function validated(Request $request, ?SampleInvoice $invoice = null): array
    {
        $header = $request->validate([
            'invoice_no' => [
                'required',
                'integer',
                Rule::unique('sample_invoices', 'invoice_no')->ignore($invoice?->id),
            ],
            'shipper_id' => ['required', 'exists:companies,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'buyer_account' => ['nullable', 'string', 'max:255'],
            'shipment_terms' => ['required', 'in:Collect,Prepaid'],
            'courier_company_id' => ['nullable', 'exists:companies,id'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'footer_note' => ['nullable', 'string'],
        ]);

        $items = $request->input('items', []);
        if (!is_array($items) || count($items) === 0) {
            $request->validate(['items' => ['required', 'array', 'min:1']]);
        }

        $cleanItems = [];
        $total = 0;

        foreach ($items as $row) {
            if (
                empty($row['art_num']) &&
                empty($row['description']) &&
                empty($row['qty']) &&
                empty($row['unit_price'])
            ) {
                continue; // skip empty rows
            }

            $qty = (int) ($row['qty'] ?? 0);
            $unit = (float) ($row['unit_price'] ?? 0);
            $sub = $qty * $unit;
            $total += $sub;

            $cleanItems[] = [
                'art_num' => $row['art_num'] ?? null,
                'description' => $row['description'] ?? null,
                'size' => $row['size'] ?? null,
                'hs_code' => $row['hs_code'] ?? null,
                'qty' => $qty,
                'unit_price' => $unit,
                'sub_total' => $sub,
            ];
        }

        if (empty($cleanItems)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'items' => ['At least one item row is required.'],
            ]);
        }

        // Build snapshots (shipper, receiver, courier)
        $shipper = Company::findOrFail($header['shipper_id']);
        $customer = Customer::findOrFail($header['customer_id']);
        $courierCompany = $header['courier_company_id']
            ? Company::findOrFail($header['courier_company_id'])
            : null;

        $shipperAddress = trim(implode("\n", array_filter([
            $shipper->company_name ?? $shipper->name,
            $shipper->address,
        ])));

        $receiverLines = [
            $customer->company_name,
            $customer->address ?? $customer->shipping_address,
            $customer->country,
            '',
            $customer->name ? 'Attention: ' . $customer->name : null,
        ];

        $receiverBlock = trim(implode("\n", array_filter($receiverLines)));

        $footerNote = $header['footer_note']
            ?? "1. Sample of no commercial value.\n2. Values provided for the Customs Purposes ONLY.";

        $headerData = [
            'invoice_no' => $header['invoice_no'],
            'shipper_id' => $shipper->id,
            'customer_id' => $customer->id,
            'courier_company_id' => $courierCompany?->id,
            'shipper_name' => $shipper->company_name ?? $shipper->name,
            'shipper_address' => $shipperAddress,
            'customer_company_name' => $customer->company_name,
            'customer_address_block' => $receiverBlock,
            'attention_name' => $customer->name,
            'buyer_account' => $header['buyer_account'] ?? null,
            'shipment_terms' => $header['shipment_terms'],
            'courier_name' => $courierCompany?->company_name ?? $courierCompany?->name,
            'tracking_number' => $header['tracking_number'] ?? null,
            'footer_note' => $footerNote,
            'total_amount' => $total,
        ];

        // Only set created_by on CREATE
        if ($invoice === null) {
            $headerData['created_by'] = auth()->id();
        }

        return [
            'header' => $headerData,
            'items' => $cleanItems,
        ];
    }
}