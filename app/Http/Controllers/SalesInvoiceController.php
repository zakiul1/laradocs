<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\Customer;
use App\Models\Shipper;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $q = SalesInvoice::with(['shipper','customer','currency'])
            ->latest('id');

        if ($request->filled('type')) $q->where('type', $request->type);
        if ($request->filled('shipper_id')) $q->where('shipper_id', $request->shipper_id);
        if ($request->filled('customer_id')) $q->where('customer_id', $request->customer_id);
        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function($w) use ($s) {
                $w->where('invoice_no','like',"%$s%")
                  ->orWhereHas('customer', fn($qq)=>$qq->where('name','like',"%$s%"))
                  ->orWhereHas('shipper', fn($qq)=>$qq->where('name','like',"%$s%"));
            });
        }

        $invoices = $q->paginate(20)->withQueryString();
        return view('sales_invoices.index', [
            'invoices'=>$invoices,
            'shippers'=>Shipper::orderBy('name')->get(),
            'customers'=>Customer::orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        $invoiceNo = (string) ( (int) (SalesInvoice::max('invoice_no') ?? 100000) + 1 );
        $defaultsLC = "1. The type of the L/C is irrevocable and transferable at sight.\n2. This L/C should be transferred by Uttara Bank Ltd.\n3. Trans-shipments and partial shipments are allowed.\n4. +/- 3% in quantity and value is accepted.\n5. Negotiations are allowed with any Bank in Bangladesh.\n6. All discrepancies will be acceptable, except late shipment, prices and quantities.\n7. Country of origin : Bangladesh.";
        $defaultsTT = "1. All the charges of sender’s and receiver’s banks are sender / purchaser’s account.\n2. PLEASE ADVISE THE TT THROUGH OUR BANK AS ABOVE";

        return view('sales_invoices.create', [
            'invoiceNo' => $invoiceNo,
            'shippers'  => Shipper::orderBy('name')->get(),
            'customers' => Customer::orderBy('name')->get(),
            'currencies'=> Currency::where('active',1)->orderBy('code')->get(),
            'default_terms_lc' => $defaultsLC,
            'default_terms_tt' => $defaultsTT,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_no' => ['required','string','max:50','unique:sales_invoices,invoice_no'],
            'type'       => ['required', Rule::in(['LC','TT'])],
            'shipper_id' => ['required','exists:shippers,id'],
            'customer_id'=> ['required','exists:customers,id'],
            'issue_date' => ['nullable','date'],
            'delivery_date'=> ['nullable','date'],
            'payment_mode'=> ['nullable','string','max:150'],
            'terms_of_shipment'=> ['nullable','string','max:150'],
            'currency_id'=> ['nullable','exists:currencies,id'],
            'commercial_cost'=> ['nullable','numeric'],
            'siatex_discount' => ['nullable','numeric'],
            'terms_and_conditions' => ['nullable','string'],
            // items[]
            'items' => ['array'],
            'items.*.art_num'   => ['nullable','string','max:100'],
            'items.*.article_description' => ['nullable','string','max:500'],
            'items.*.size'      => ['nullable','string','max:100'],
            'items.*.hs_code'   => ['nullable','string','max:50'],
            'items.*.qty'       => ['nullable','numeric'],
            'items.*.unit_price'=> ['nullable','numeric'],
        ]);

        $invoice = SalesInvoice::create([
            ...collect($data)->except('items')->toArray(),
            'commercial_cost' => $data['commercial_cost'] ?? 0,
            'siatex_discount'  => $data['siatex_discount'] ?? 0,
            'created_by'       => $request->user()->id,
        ]);

        foreach ($data['items'] ?? [] as $i => $row) {
            if (empty($row['article_description']) && empty($row['qty'])) continue;
            SalesInvoiceItem::create([
                'sales_invoice_id' => $invoice->id,
                'art_num' => $row['art_num'] ?? null,
                'article_description' => $row['article_description'] ?? null,
                'size' => $row['size'] ?? null,
                'hs_code' => $row['hs_code'] ?? null,
                'qty' => (float)($row['qty'] ?? 0),
                'unit_price' => (float)($row['unit_price'] ?? 0),
                'sort' => $i,
            ]);
        }

        $invoice->recalcTotals();
        $invoice->save();

        return redirect()->route('admin.sales-invoices.show', $invoice)->with('success', 'Invoice created.');
    }

    public function show(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load(['items','shipper','customer','currency']);
        return view('sales_invoices.preview', ['invoice'=>$salesInvoice]);
    }

    public function edit(SalesInvoice $salesInvoice)
    {
        $salesInvoice->load('items');
        return view('sales_invoices.edit', [
            'invoice'=>$salesInvoice,
            'shippers'=>Shipper::orderBy('name')->get(),
            'customers'=>Customer::orderBy('name')->get(),
            'currencies'=>Currency::where('active',1)->orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, SalesInvoice $salesInvoice)
    {
        $data = $request->validate([
            'type'       => ['required', Rule::in(['LC','TT'])],
            'shipper_id' => ['required','exists:shippers,id'],
            'customer_id'=> ['required','exists:customers,id'],
            'issue_date' => ['nullable','date'],
            'delivery_date'=> ['nullable','date'],
            'payment_mode'=> ['nullable','string','max:150'],
            'terms_of_shipment'=> ['nullable','string','max:150'],
            'currency_id'=> ['nullable','exists:currencies,id'],
            'commercial_cost'=> ['nullable','numeric'],
            'siatex_discount' => ['nullable','numeric'],
            'terms_and_conditions' => ['nullable','string'],
            'items' => ['array'],
            'items.*.id'        => ['nullable','exists:sales_invoice_items,id'],
            'items.*.delete'    => ['nullable','boolean'],
            'items.*.art_num'   => ['nullable','string','max:100'],
            'items.*.article_description' => ['nullable','string','max:500'],
            'items.*.size'      => ['nullable','string','max:100'],
            'items.*.hs_code'   => ['nullable','string','max:50'],
            'items.*.qty'       => ['nullable','numeric'],
            'items.*.unit_price'=> ['nullable','numeric'],
        ]);

        $salesInvoice->fill(collect($data)->except('items')->toArray())->save();

        // Upsert items
        foreach ($data['items'] ?? [] as $i => $row) {
            if (!empty($row['delete']) && !empty($row['id'])) {
                SalesInvoiceItem::where('id',$row['id'])->where('sales_invoice_id',$salesInvoice->id)->delete();
                continue;
            }
            if (!empty($row['id'])) {
                $item = SalesInvoiceItem::where('id',$row['id'])->where('sales_invoice_id',$salesInvoice->id)->firstOrFail();
                $item->fill([
                    'art_num'=>$row['art_num'] ?? null,
                    'article_description'=>$row['article_description'] ?? null,
                    'size'=>$row['size'] ?? null,
                    'hs_code'=>$row['hs_code'] ?? null,
                    'qty'=>(float)($row['qty'] ?? 0),
                    'unit_price'=>(float)($row['unit_price'] ?? 0),
                    'sort'=>$i
                ])->save();
            } else {
                if (empty($row['article_description']) && empty($row['qty'])) continue;
                SalesInvoiceItem::create([
                    'sales_invoice_id'=>$salesInvoice->id,
                    'art_num'=>$row['art_num'] ?? null,
                    'article_description'=>$row['article_description'] ?? null,
                    'size'=>$row['size'] ?? null,
                    'hs_code'=>$row['hs_code'] ?? null,
                    'qty'=>(float)($row['qty'] ?? 0),
                    'unit_price'=>(float)($row['unit_price'] ?? 0),
                    'sort'=>$i
                ]);
            }
        }

        $salesInvoice->recalcTotals();
        $salesInvoice->save();

        return redirect()->route('admin.sales-invoices.show', $salesInvoice)->with('success','Invoice updated.');
    }

    public function destroy(SalesInvoice $salesInvoice)
    {
        $salesInvoice->delete();
        return redirect()->route('admin.sales-invoices.index')->with('success','Invoice deleted.');
    }
    public function pdf(SalesInvoice $salesInvoice)
{
    $salesInvoice->load(['items','shipper','customer','currency']);
    $pdf = Pdf::loadView('sales_invoices.pdf', ['invoice'=>$salesInvoice])
        ->setPaper('a4', 'portrait');
    return $pdf->download('SalesInvoice_'.$salesInvoice->invoice_no.'.pdf');
}
}
