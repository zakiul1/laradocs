{{-- resources/views/sales_invoices/pdf.blade.php --}}
@php
    /** @var \App\Models\SalesInvoice $invoice */

    $currencyCode = $invoice->currency_code ?? (optional($invoice->currency)->code ?? '');
    $grandTotal = $invoice->grand_total ?? 0;

    // Bank & purchaser blocks come from snapshots so that they stay exactly as edited
    $bankBlock = $invoice->bank_snapshot;
    $purchaserBlock = $invoice->purchaser_snapshot;

    $issueDate = optional($invoice->issue_date)->format('jS M Y');
    $deliveryDate = optional($invoice->delivery_date)->format('jS M Y');
    $paymentMode = $invoice->payment_mode;
    $termsShipment = $invoice->terms_of_shipment;

    // Items – always use collection so pdf works from DB & from preview
    $items = collect($invoice->items ?? []);
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .page {
            width: 100%;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p {
            margin: 0;
            padding: 0;
        }

        .header-row {
            width: 100%;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .company-block {
            float: left;
            width: 60%;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
        }

        .invoice-title-block {
            float: right;
            width: 35%;
            text-align: right;
        }

        .invoice-title {
            font-size: 16px;
            font-weight: bold;
        }

        .invoice-number-box {
            display: inline-block;
            border: 1px solid #000;
            padding: 4px 20px;
            margin-top: 4px;
            font-weight: bold;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .box {
            border: 1px solid #000;
            padding: 6px;
            min-height: 80px;
        }

        .box-title {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .mt-8 {
            margin-top: 8px;
        }

        .mt-12 {
            margin-top: 12px;
        }

        .mb-4 {
            margin-bottom: 4px;
        }

        .mb-8 {
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid #000;
        }

        .table th,
        .table td {
            padding: 3px 4px;
        }

        .table th {
            font-weight: bold;
            text-align: left;
        }

        .table th.text-right,
        .table td.text-right {
            text-align: right;
        }

        .table th.text-center,
        .table td.text-center {
            text-align: center;
        }

        .info-table th,
        .info-table td {
            font-size: 10px;
            padding: 4px 4px;
        }

        .no-border {
            border: none !important;
        }

        .total-row td {
            border-top: 1px solid #000;
            font-weight: bold;
        }

        .terms-title {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .terms-list {
            margin-top: 3px;
        }

        .terms-list p {
            margin-bottom: 2px;
        }

        .footer-bar {
            margin-top: 16px;
            border: 1px solid #000;
            text-align: center;
            padding: 6px 0;
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- HEADER --}}
        <div class="header-row clearfix">
            <div class="company-block">
                <div class="company-name">
                    {{ $invoice->shipper_name ?? 'SIATEX (BD) LIMITED' }}
                </div>
                @if ($invoice->shipper_address)
                    <div class="small">
                        {!! nl2br(e($invoice->shipper_address)) !!}
                    </div>
                @endif
            </div>

            <div class="invoice-title-block">
                <div class="invoice-title">
                    {{ $invoice->invoice_type === 'TT' ? 'COMMERCIAL INVOICE' : 'PROFORMA INVOICE' }}
                </div>
                <div class="invoice-number-box">
                    {{ $invoice->invoice_no }}
                </div>
            </div>
        </div>

        {{-- BANK & RECEIVER --}}
        <table class="table">
            <tr>
                <th width="50%">Our Bank Address:</th>
                <th width="50%">Receiver:</th>
            </tr>
            <tr style="vertical-align: top;">
                <td>
                    {!! nl2br(e($bankBlock)) !!}
                </td>
                <td>
                    {!! nl2br(e($purchaserBlock)) !!}
                </td>
            </tr>
        </table>

        {{-- ISSUE / DELIVERY / PAYMENT / TERMS / CURRENCY --}}
        <table class="table info-table mt-8">
            <tr>
                <th>ISSUE DATE</th>
                <th>DELIVERY DATE</th>
                <th>PAYMENT MODE</th>
                <th>TERMS OF SHIPMENT</th>
                <th>CURRENCY</th>
            </tr>
            <tr>
                <td>{{ $issueDate }}</td>
                <td>{{ $deliveryDate }}</td>
                <td>{{ $paymentMode }}</td>
                <td>{{ $termsShipment }}</td>
                <td>{{ $currencyCode }}</td>
            </tr>
        </table>

        {{-- ITEMS TABLE --}}
        <table class="table mt-8">
            <thead>
                <tr>
                    <th width="8%">Art Num</th>
                    <th>ARTICLE DESCRIPTION</th>
                    <th width="8%">SIZE</th>
                    <th width="8%" class="text-center">QTY</th>
                    <th width="10%" class="text-right">UNIT PRICE</th>
                    <th width="14%" class="text-right">SUB TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $row)
                    @php
                        // row can be model or array (for preview)
                        $artNum = is_array($row) ? $row['art_num'] ?? '' : $row->art_num;
                        $description = is_array($row) ? $row['description'] ?? '' : $row->description;
                        $size = is_array($row) ? $row['size'] ?? '' : $row->size;
                        $qty = (float) (is_array($row) ? $row['qty'] ?? 0 : $row->qty);
                        $unitPrice = (float) (is_array($row) ? $row['unit_price'] ?? 0 : $row->unit_price);
                        $subTotal = $qty * $unitPrice;
                    @endphp
                    <tr>
                        <td>{{ $artNum }}</td>
                        <td>{{ $description }}</td>
                        <td>{{ $size }}</td>
                        <td class="text-center">{{ $qty ? number_format($qty, 0) : '' }}</td>
                        <td class="text-right">{{ $unitPrice ? number_format($unitPrice, 2) : '' }}</td>
                        <td class="text-right">{{ $subTotal ? number_format($subTotal, 2) : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Price :</strong></td>
                    <td class="text-right">
                        {{ $currencyCode ? $currencyCode . ' ' : '' }}{{ number_format($grandTotal, 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- TERMS & CONDITIONS --}}
        <div class="mt-12">
            <div class="terms-title">Terms and Condition (required in the L/C):</div>
            <div class="terms-list">
                {!! nl2br(e($invoice->terms_conditions)) !!}
            </div>
        </div>

        {{-- REMARKS --}}
        <div class="mt-8">
            <div class="terms-title">Remarks:</div>
            <div class="small">
                {!! nl2br(e($invoice->message_body)) !!}
            </div>
        </div>

        {{-- FOOTER BAR --}}
        <div class="footer-bar">
            {{ $invoice->footer_note }}
        </div>
    </div>
</body>

</html>
