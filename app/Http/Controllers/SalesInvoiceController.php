<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $shipperId = $request->get('shipper_id');
        $customerId = $request->get('customer_id');
        $q = trim((string) $request->get('q'));

        $invoices = SalesInvoice::query()
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
            ->paginate(20)
            ->withQueryString();

        $shippers = Company::whereHas('category', function ($q) {
            $q->where('name', 'Shipper')
                ->orWhere('slug', 'shipper');
        })
            ->orderBy('company_name')
            ->get();

        $customers = Customer::orderBy('company_name')->orderBy('name')->get();

        return view('sales_invoices.index', compact('invoices', 'shippers', 'customers'));
    }

    public function create(Request $request)
    {
        $shipperId = $request->get('shipper_id');
        $customerId = $request->get('customer_id');

        $shippers = Company::whereHas('category', function ($q) {
            $q->where('name', 'Shipper')
                ->orWhere('slug', 'shipper');
        })
            ->orderBy('company_name')
            ->get();

        $customers = Customer::orderBy('company_name')->orderBy('name')->get();
        $currencies = Currency::orderBy('code')->get();

        $nextInvoiceNo = (int) (SalesInvoice::max('invoice_no') ?? 100200) + 1;

        $defaultType = 'LC';
        $defaultTermsLc = $this->defaultTerms('LC');
        $defaultTermsTt = $this->defaultTerms('TT');
        $defaultFobBody = $this->defaultMessageBody('FOB');
        $defaultCifBody = $this->defaultMessageBody('CIF');

        // ------------------------------------------------------------------
        // Build lookup arrays for Alpine:
        //   bankBlocks[shipper_id]   => "Our Bank Address text..."
        //   customerBlocks[customer_id] => "Purchaser block text..."
        // ------------------------------------------------------------------
        $bankBlocks = [];
        foreach ($shippers as $shipper) {
            $bank = Bank::where('type', 'Shipper Bank')
                ->where('company_id', $shipper->id)
                ->first();

            if ($bank) {
                $bankBlocks[$shipper->id] = trim(implode("\n", array_filter([
                    $bank->bank_account ? 'Account # ' . $bank->bank_account : null,
                    $bank->name,
                    $bank->address,
                    $bank->country,
                    $bank->swift_code ? 'Swift: ' . $bank->swift_code : null,
                ])));
            } else {
                $bankBlocks[$shipper->id] = '';   // still define key so JS finds it
            }
        }

        $customerBlocks = [];
        foreach ($customers as $customer) {
            $customerBlocks[$customer->id] = trim(implode("\n", array_filter([
                $customer->company_name ?: $customer->name,
                $customer->address ?: $customer->shipping_address,
                $customer->country,
            ])));
        }

        return view('sales_invoices.create', [
            'shippers' => $shippers,
            'customers' => $customers,
            'currencies' => $currencies,
            'shipperId' => $shipperId,
            'customerId' => $customerId,
            'nextInvoiceNo' => $nextInvoiceNo,
            'defaultType' => $defaultType,
            'defaultTermsLc' => $defaultTermsLc,
            'defaultTermsTt' => $defaultTermsTt,
            'defaultFobBody' => $defaultFobBody,
            'defaultCifBody' => $defaultCifBody,
            'bankBlocks' => $bankBlocks,
            'customerBlocks' => $customerBlocks,
        ]);
    }


    public function store(Request $request)
    {
        $data = $this->validated($request);

        DB::beginTransaction();
        try {
            $invoice = SalesInvoice::create($data['header']);

            foreach ($data['items'] as $item) {
                $item['sales_invoice_id'] = $invoice->id;
                SalesInvoiceItem::create($item);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if ($request->has('preview')) {
            return redirect()->route('admin.sales-invoices.show', $invoice);
        }

        return redirect()
            ->route('admin.sales-invoices.index')
            ->with('success', 'Sales invoice created.');
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load('items');

        return view('sales_invoices.show', [
            'invoice' => $salesInvoice,
        ]);
    }

    public function edit(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load('items');

        $shippers = Company::whereHas('category', function ($q) {
            $q->where('name', 'Shipper')
                ->orWhere('slug', 'shipper');
        })
            ->orderBy('company_name')
            ->get();

        $customers = Customer::orderBy('company_name')->orderBy('name')->get();
        $currencies = Currency::orderBy('code')->get();

        // All shipper banks (or filter by shipper_id if you want)
        $banks = Bank::where('type', 'Shipper Bank')
            ->orderBy('name')
            ->get();
        // if you want only banks of this shipper:
        // $banks = Bank::where('type', 'Shipper Bank')
        //              ->where('company_id', $salesInvoice->shipper_id)
        //              ->orderBy('name')
        //              ->get();

        // Defaults expected by the Blade
        $defaultPaymentModeLC = 'Transferable L/C at sight';
        $defaultPaymentModeTT = 'Telegraphic Transfer';

        $defaultTermsLC = $this->defaultTerms('LC');
        $defaultTermsTT = $this->defaultTerms('TT');

        $defaultFobMessage = $this->defaultMessageBody('FOB');
        $defaultCifMessage = $this->defaultMessageBody('CIF');

        return view('sales_invoices.edit', [
            'invoice' => $salesInvoice,
            'shippers' => $shippers,
            'customers' => $customers,
            'currencies' => $currencies,
            'banks' => $banks,                 // <-- important
            'defaultPaymentModeLC' => $defaultPaymentModeLC,
            'defaultPaymentModeTT' => $defaultPaymentModeTT,
            'defaultTermsLC' => $defaultTermsLC,
            'defaultTermsTT' => $defaultTermsTT,
            'defaultFobMessage' => $defaultFobMessage,
            'defaultCifMessage' => $defaultCifMessage,
        ]);
    }



    public function update(Request $request, SalesInvoice $salesInvoice)
    {
        $data = $this->validated($request, $salesInvoice);

        DB::beginTransaction();
        try {
            $salesInvoice->update($data['header']);

            $salesInvoice->items()->delete();
            foreach ($data['items'] as $item) {
                $item['sales_invoice_id'] = $salesInvoice->id;
                SalesInvoiceItem::create($item);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        if ($request->has('preview')) {
            return redirect()->route('admin.sales-invoices.show', $salesInvoice);
        }

        return redirect()
            ->route('admin.sales-invoices.index')
            ->with('success', 'Sales invoice updated.');
    }

    public function destroy(SalesInvoice $salesInvoice)
    {
        $salesInvoice->delete();

        return redirect()
            ->route('admin.sales-invoices.index')
            ->with('success', 'Sales invoice deleted.');
    }

    public function pdf(SalesInvoice $salesInvoice)
    {
        $invoice = $salesInvoice->load(['items', 'currency']); // adjust relations as you have them

        $pdf = Pdf::loadView('sales_invoices.pdf', [
            'invoice' => $invoice,
        ])->setPaper('a4', 'portrait');

        $fileName = 'Sales-Invoice-' . $invoice->invoice_no . '.pdf';

        // open in browser
        // return $pdf->stream($fileName);

        // force download
        return $pdf->download($fileName);
    }

    // ------------------------------------------------------------------
    // VALIDATION + SNAPSHOT BUILDING
    // ------------------------------------------------------------------

    protected function validated(Request $request, ?SalesInvoice $existing = null): array
    {
        $invoiceId = $existing?->id;

        // ------------------------------------------------------
        // 1) HEADER VALIDATION
        // ------------------------------------------------------
        $header = $request->validate([
            'invoice_no' => ['required', 'integer', 'unique:sales_invoices,invoice_no,' . ($invoiceId ?? 'NULL') . ',id'],
            'invoice_type' => ['required', 'in:LC,TT'],
            'shipper_id' => ['required', 'exists:companies,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'issue_date' => ['required', 'date'],
            'delivery_date' => ['nullable', 'date'],
            'payment_mode' => ['required', 'string', 'max:255'],
            'terms_of_shipment' => ['nullable', 'string', 'max:255'],
            'currency_id' => ['required', 'exists:currencies,id'],

            // editable snapshots
            'bank_snapshot' => ['nullable', 'string'],
            'purchaser_snapshot' => ['nullable', 'string'],

            // extra
            'terms_conditions' => ['nullable', 'string'],
            'message_type' => ['required', 'in:FOB,CIF'],
            'message_body' => ['nullable', 'string'],
            'footer_note' => ['nullable', 'string'],
            'commercial_cost' => ['nullable', 'numeric', 'min:0'],
            'siatex_discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        // ------------------------------------------------------
        // 2) ITEMS VALIDATION
        // ------------------------------------------------------
        $itemsInput = $request->input('items', []);
        if (!is_array($itemsInput) || count($itemsInput) === 0) {
            $request->validate(['items' => ['required', 'array', 'min:1']]);
        }

        $items = [];
        $itemsTotal = 0;
        $totalQty = 0;

        foreach ($itemsInput as $row) {
            if (
                empty($row['art_num']) &&
                empty($row['description']) &&
                empty($row['size']) &&
                empty($row['qty']) &&
                empty($row['unit_price'])
            ) {
                continue;
            }

            $qty = (int) ($row['qty'] ?? 0);
            $unit = (float) ($row['unit_price'] ?? 0);
            $sub = $qty * $unit;

            $itemsTotal += $sub;
            $totalQty += $qty;

            $items[] = [
                'art_num' => $row['art_num'] ?? null,
                'description' => $row['description'] ?? null,
                'size' => $row['size'] ?? null,
                'qty' => $qty,
                'unit_price' => $unit,
                'sub_total' => $sub,
            ];
        }

        if (empty($items)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'items' => ['At least one item row is required.'],
            ]);
        }

        // ------------------------------------------------------
        // 3) FETCH RELATED MODELS
        // ------------------------------------------------------
        $shipper = Company::findOrFail($header['shipper_id']);
        $customer = Customer::findOrFail($header['customer_id']);
        $currency = Currency::findOrFail($header['currency_id']);

        $commercialCost = (float) ($header['commercial_cost'] ?? 0);
        $siatexDiscount = (float) ($header['siatex_discount'] ?? 0);
        $grandTotal = max(0, $itemsTotal + $commercialCost - $siatexDiscount);

        // ------------------------------------------------------
        // 4) SHIPPER ADDRESS SNAPSHOT  (required in DB)
        // ------------------------------------------------------
        $shipperAddress = trim(implode("\n", array_filter([
            $shipper->company_name ?? $shipper->name,
            $shipper->address,
        ])));

        if ($shipperAddress === '') {
            $shipperAddress = 'N/A'; // failsafe if company has no address
        }

        // ------------------------------------------------------
        // 5) AUTO-FILL BANK SNAPSHOT
        // ------------------------------------------------------
        $bankBlock = trim($header['bank_snapshot'] ?? '');

        if ($bankBlock === '') {
            $bank = Bank::where('type', 'Shipper Bank')
                ->where('company_id', $shipper->id)
                ->first();

            if ($bank) {
                $bankBlock = trim(implode("\n", array_filter([
                    $bank->bank_account ? 'Account # ' . $bank->bank_account : null,
                    $bank->name,
                    $bank->address,
                    $bank->country,
                    $bank->swift_code ? 'Swift: ' . $bank->swift_code : null,
                ])));
            }
        }

        // ------------------------------------------------------
        // 6) AUTO-FILL PURCHASER SNAPSHOT
        // ------------------------------------------------------
        $purchaserBlock = trim($header['purchaser_snapshot'] ?? '');

        if ($purchaserBlock === '') {
            $purchaserBlock = trim(implode("\n", array_filter([
                $customer->company_name ?: $customer->name,
                $customer->address ?: $customer->shipping_address,
                $customer->country,
            ])));
        }

        // ------------------------------------------------------
        // 7) DEFAULT TEXTS
        // ------------------------------------------------------
        if (empty($header['terms_conditions'])) {
            $header['terms_conditions'] = $this->defaultTerms($header['invoice_type']);
        }

        if (empty($header['message_body'])) {
            $header['message_body'] = $this->defaultMessageBody($header['message_type']);
        }

        if (empty($header['footer_note'])) {
            $header['footer_note'] =
                $header['invoice_type'] === 'TT'
                ? 'PLEASE ADVISED THE TT THROUGH OUR BANK AS ABOVE'
                : 'PLEASE ADVISED THE L/C THROUGH OUR BANK AS ABOVE';
        }

        // ------------------------------------------------------
        // 8) FINAL HEADER PAYLOAD
        // ------------------------------------------------------
        $headerData = [
            'invoice_no' => $header['invoice_no'],
            'invoice_type' => $header['invoice_type'],
            'shipper_id' => $shipper->id,
            'customer_id' => $customer->id,

            'shipper_name' => $shipper->company_name ?? $shipper->name,
            'shipper_address' => $shipperAddress,     // REQUIRED – fixes SQL error
            'customer_company_name' => $customer->company_name,
            'customer_address_block' => $purchaserBlock,

            'bank_snapshot' => $bankBlock,
            'purchaser_snapshot' => $purchaserBlock,

            'issue_date' => $header['issue_date'],
            'delivery_date' => $header['delivery_date'] ?? null,
            'payment_mode' => $header['payment_mode'],
            'terms_of_shipment' => $header['terms_of_shipment'] ?? null,

            'currency_id' => $currency->id,
            'currency_code' => $currency->code,

            'items_total' => $itemsTotal,
            'total_qty' => $totalQty,
            'commercial_cost' => $commercialCost,
            'siatex_discount' => $siatexDiscount,
            'total_amount' => $grandTotal,

            'terms_conditions' => $header['terms_conditions'],
            'message_type' => $header['message_type'],
            'message_body' => $header['message_body'],
            'footer_note' => $header['footer_note'],

            'created_by' => $existing?->created_by ?? auth()->id(),
        ];

        return [
            'header' => $headerData,
            'items' => $items,
        ];
    }
    public function previewEdit(Request $request, SalesInvoice $salesInvoice)
    {
        $data = $this->validated($request, $salesInvoice);

        // Do NOT update database, only show preview
        $previewInvoice = new SalesInvoice($data['header']);
        $previewInvoice->items = collect($data['items']);

        return view('sales_invoices.show', [
            'invoice' => $previewInvoice,
            'preview' => true
        ]);
    }



    // ------------------------------------------------------------------
    // Default text helpers
    // ------------------------------------------------------------------

    protected function defaultTerms(string $type): string
    {
        if ($type === 'TT') {
            return "1. All the charges of sender's and receiver's banks are sender / purchaser's account.";
        }

        return implode("\n", [
            '1. The type of the L/C is irrevocable and transferable at sight.',
            '2. This L/C should be transferred by Uttara Bank Ltd.',
            '3. Trans-shipments and partial shipments are allowed.',
            '4. +/- 3% in quantity and value is accepted.',
            '5. Negotiations are allowed with any Bank in Bangladesh.',
            '6. All discrepancies will be acceptable, except late shipment, prices and quantities.',
            '7. Country of origin: Bangladesh.',
        ]);
    }

    protected function defaultMessageBody(string $type): string
    {
        if ($type === 'CIF') {
            // your CIF default (from screenshots)
            return 'H.S Code: 61.05.1000';
        }

        return 'Please keep the following words in the B/L or Airway bill terms in the L/C:
"Full set clean on board ocean bill of lading / airway bill made out to the order of the negotiating bank in Bangladesh and endorsed to the L/C opening bank marked freight Collect."';
    }
}