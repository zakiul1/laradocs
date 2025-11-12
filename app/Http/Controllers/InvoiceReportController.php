<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\Shipper;
use App\Models\Customer;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceReportController extends Controller
{
    public function sales(Request $request)
    {
        // Filters
        $shipperId = $request->integer('shipper_id');
        $customerId = $request->integer('customer_id');
        $currencyId = $request->integer('currency_id');
        $type = $request->get('type'); // LC/TT
        $from = $request->date('from');
        $to   = $request->date('to');

        $q = SalesInvoice::query();
        if ($shipperId) $q->where('shipper_id',$shipperId);
        if ($customerId) $q->where('customer_id',$customerId);
        if ($currencyId) $q->where('currency_id',$currencyId);
        if ($type) $q->where('type',$type);
        if ($from) $q->whereDate('issue_date','>=',$from);
        if ($to)   $q->whereDate('issue_date','<=',$to);

        // Group by Fiscal Year label like "2024-25"
        $rows = $q->select([
                DB::raw("CASE 
                    WHEN MONTH(issue_date) >= 7 THEN CONCAT(YEAR(issue_date), '-', RIGHT(YEAR(issue_date)+1,2))
                    ELSE CONCAT(YEAR(issue_date)-1, '-', RIGHT(YEAR(issue_date),2))
                END as fy_label"),
                DB::raw('SUM(grand_total) as amount')
            ])
            ->groupBy('fy_label')
            ->orderBy('fy_label')
            ->get();

        return view('reports.sales', [
            'rows' => $rows,
            'filters' => [
                'shippers'=>Shipper::orderBy('name')->get(),
                'customers'=>Customer::orderBy('name')->get(),
                'currencies'=>Currency::orderBy('code')->get(),
            ]
        ]);
    }
}
