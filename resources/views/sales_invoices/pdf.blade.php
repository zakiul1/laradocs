<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body{font-family: DejaVu Sans, Helvetica, Arial, sans-serif; font-size:12px; color:#222;}
        .tbl{width:100%; border-collapse:collapse;}
        .tbl th,.tbl td{border:1px solid #ddd; padding:6px;}
        .muted{color:#666;}
        .box{border:1px solid #ddd; padding:8px;}
        .right{text-align:right}
        .mb-10{margin-bottom:10px}
        .mb-6{margin-bottom:6px}
        .title{font-weight:700; font-size:16px;}
    </style>
</head>
<body>
    <table class="tbl" style="border:none">
        <tr style="border:none">
            <td style="border:none">
                <div class="title">{{ $invoice->shipper->name ?? '' }}</div>
                <div class="muted">Our Bank Address:</div>
                <div class="mb-10">City Bank limited,<br>Account # 1401820958001,<br>Gulshan Branch , Dhaka,<br>Bangladesh</div>
            </td>
            <td style="border:none" class="right">
                <div class="muted">PROFORMA INVOICE</div>
                <div class="box">INVOICE No: {{ $invoice->invoice_no }}</div>
                <div class="mb-10"></div>
                <div class="muted">Purchaser :</div>
                <div>{{ $invoice->customer->name ?? '' }}</div>
            </td>
        </tr>
    </table>

    <table class="tbl mb-10">
        <tr>
            <th>ISSUE DATE</th><th>DELIVERY DATE</th><th>PAYMENT MODE</th><th>TERMS OF SHIPMENT</th><th>CURRENCY</th>
        </tr>
        <tr>
            <td>{{ optional($invoice->issue_date)->toDateString() }}</td>
            <td>{{ optional($invoice->delivery_date)->toDateString() }}</td>
            <td>{{ $invoice->payment_mode }}</td>
            <td>{{ $invoice->terms_of_shipment }}</td>
            <td>{{ $invoice->currency->code ?? '' }}</td>
        </tr>
    </table>

    <table class="tbl mb-10">
        <thead>
        <tr>
            <th>Art Num</th><th>ARTICLE DESCRIPTION</th><th>SIZE</th><th>HS CODE</th>
            <th class="right">QTY</th><th class="right">UNIT PRICE</th><th class="right">SUB TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $row)
        <tr>
            <td>{{ $row->art_num }}</td>
            <td>{{ $row->article_description }}</td>
            <td>{{ $row->size }}</td>
            <td>{{ $row->hs_code }}</td>
            <td class="right">{{ number_format($row->qty,3) }}</td>
            <td class="right">{{ number_format($row->unit_price,4) }}</td>
            <td class="right">{{ number_format($row->sub_total,2) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="6" class="right">Items Total :</td>
            <td class="right">{{ number_format($invoice->items_total,2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="right">Commercial cost :</td>
            <td class="right">{{ number_format($invoice->commercial_cost,2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="right">Siatex Discount :</td>
            <td class="right">-{{ number_format($invoice->siatex_discount,2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="right"><strong>Total :</strong></td>
            <td class="right"><strong>{{ number_format($invoice->grand_total,2) }}</strong></td>
        </tr>
        </tbody>
    </table>

    <div class="title mb-6">Terms and Condition {{ $invoice->type==='LC' ? '(required in the L/C)' : '' }}:</div>
    <div style="white-space:pre-line">{{ $invoice->terms_and_conditions }}</div>
</body>
</html>
