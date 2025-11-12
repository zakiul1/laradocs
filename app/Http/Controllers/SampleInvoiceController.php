<?php

namespace App\Http\Controllers;

use App\Models\SampleInvoice;
use App\Models\SampleInvoiceItem;
use App\Models\Customer;
use App\Models\Shipper;
use App\Models\Currency;
use Illuminate\Http\Request;

class SampleInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $q = SampleInvoice::with(['shipper','customer','currency'])->latest('id');
        if ($s = $request->get('search')) {
            $q->where('invoice_no','like',"%$s%")
              ->orWhereHas('customer', fn($qq)=>$qq->where('name','like',"%$s%"))
              ->orWhereHas('shipper', fn($qq)=>$qq->where('name','like',"%$s%"));
        }
        $invoices = $q->paginate(20)->withQueryString();
        return view('sample_invoices.index', [
            'invoices'=>$invoices,
            'shippers'=>Shipper::orderBy('name')->get(),
            'customers'=>Customer::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $invoiceNo = (string) ( (int) (SampleInvoice::max('invoice_no') ?? 101000) + 1 );
        return view('sample_invoices.create', [
            'invoiceNo'=>$invoiceNo,
            'shippers'=>Shipper::orderBy('name')->get(),
            'customers'=>Customer::orderBy('name')->get(),
            'currencies'=>Currency::where('active',1)->orderBy('code')->get(),
            'default_footer'=>"1. Sample of no commercial value.\n2. Values provided for the Customs Purposes ONLY."
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_no' => ['required','string','max:50','unique:sample_invoices,invoice_no'],
            'shipper_id' => ['required','exists:shippers,id'],
            'customer_id'=> ['required','exists:customers,id'],
            'buyer_account'=> ['nullable','string','max:150'],
            'shipment_terms'=> ['nullable','in:Collect,Prepaid'],
            'courier_name'=> ['nullable','string','max:150'],
            'tracking_number'=> ['nullable','string','max:150'],
            'currency_id' => ['nullable','exists:currencies,id'],
            'footer_note' => ['nullable','string'],
            'items' => ['array'],
            'items.*.art_num' => ['nullable','string','max:100'],
            'items.*.article_description' => ['nullable','string','max:500'],
            'items.*.size' => ['nullable','string','max:100'],
            'items.*.hs_code' => ['nullable','string','max:50'],
            'items.*.qty' => ['nullable','numeric'],
            'items.*.unit_price' => ['nullable','numeric'],
        ]);

        $invoice = SampleInvoice::create([
            ...collect($data)->except('items')->toArray(),
            'created_by' => $request->user()->id,
        ]);

        foreach ($data['items'] ?? [] as $i => $row) {
            if (empty($row['article_description']) && empty($row['qty'])) continue;
            SampleInvoiceItem::create([
                'sample_invoice_id'=>$invoice->id,
                'art_num'=>$row['art_num'] ?? null,
                'article_description'=>$row['article_description'] ?? null,
                'size'=>$row['size'] ?? null,
                'hs_code'=>$row['hs_code'] ?? null,
                'qty'=>(float)($row['qty'] ?? 0),
                'unit_price'=>(float)($row['unit_price'] ?? 0),
                'sort'=>$i
            ]);
        }

        $invoice->recalcTotals();
        $invoice->save();

        return redirect()->route('admin.sample-invoices.show', $invoice)->with('success','Sample Invoice created.');
    }

    public function show(SampleInvoice $sampleInvoice)
    {
        $sampleInvoice->load(['items','shipper','customer','currency']);
        return view('sample_invoices.preview', ['invoice'=>$sampleInvoice]);
    }

    public function edit(SampleInvoice $sampleInvoice)
    {
        $sampleInvoice->load('items');
        return view('sample_invoices.edit', [
            'invoice'=>$sampleInvoice,
            'shippers'=>Shipper::orderBy('name')->get(),
            'customers'=>Customer::orderBy('name')->get(),
            'currencies'=>Currency::where('active',1)->orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, SampleInvoice $sampleInvoice)
    {
        $data = $request->validate([
            'shipper_id' => ['required','exists:shippers,id'],
            'customer_id'=> ['required','exists:customers,id'],
            'buyer_account'=> ['nullable','string','max:150'],
            'shipment_terms'=> ['nullable','in:Collect,Prepaid'],
            'courier_name'=> ['nullable','string','max:150'],
            'tracking_number'=> ['nullable','string','max:150'],
            'currency_id' => ['nullable','exists:currencies,id'],
            'footer_note' => ['nullable','string'],
            'items' => ['array'],
            'items.*.id' => ['nullable','exists:sample_invoice_items,id'],
            'items.*.delete' => ['nullable','boolean'],
            'items.*.art_num' => ['nullable','string','max:100'],
            'items.*.article_description' => ['nullable','string','max:500'],
            'items.*.size' => ['nullable','string','max:100'],
            'items.*.hs_code' => ['nullable','string','max:50'],
            'items.*.qty' => ['nullable','numeric'],
            'items.*.unit_price' => ['nullable','numeric'],
        ]);

        $sampleInvoice->fill(collect($data)->except('items')->toArray())->save();

        foreach ($data['items'] ?? [] as $i => $row) {
            if (!empty($row['delete']) && !empty($row['id'])) {
                SampleInvoiceItem::where('id',$row['id'])->where('sample_invoice_id',$sampleInvoice->id)->delete();
                continue;
            }
            if (!empty($row['id'])) {
                $item = SampleInvoiceItem::where('id',$row['id'])->where('sample_invoice_id',$sampleInvoice->id)->firstOrFail();
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
                SampleInvoiceItem::create([
                    'sample_invoice_id'=>$sampleInvoice->id,
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

        $sampleInvoice->recalcTotals();
        $sampleInvoice->save();

        return redirect()->route('admin.sample-invoices.show',$sampleInvoice)->with('success','Sample Invoice updated.');
    }

    public function destroy(SampleInvoice $sampleInvoice)
    {
        $sampleInvoice->delete();
        return redirect()->route('admin.sample-invoices.index')->with('success','Sample Invoice deleted.');
    }
    public function pdf(SampleInvoice $sampleInvoice)
{
    $sampleInvoice->load(['items','shipper','customer','currency']);
    $pdf = Pdf::loadView('sample_invoices.pdf', ['invoice'=>$sampleInvoice])
        ->setPaper('a4','portrait');
    return $pdf->download('SampleInvoice_'.$sampleInvoice->invoice_no.'.pdf');
}
}
