<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sample Invoice {{ $invoice->invoice_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 4px;
        }

        .no-border {
            border: none;
        }
    </style>
</head>

<body>
    <table class="no-border" style="border:none;">
        <tr class="no-border">
            <td class="no-border" style="width:70%;">
                <strong>{{ $invoice->shipper_name }}</strong><br>
                {!! nl2br(e($invoice->shipper_address)) !!}
            </td>
            <td class="no-border" style="text-align:right;">
                <strong>INVOICE No.</strong><br>
                {{ $invoice->invoice_no }}
            </td>
        </tr>
    </table>

    <br>

    <table>
        <tr>
            <td style="width:50%; vertical-align:top;">
                <strong>Details:</strong><br><br>
                Buyer Account: {{ $invoice->buyer_account }}<br>
                Shipment Terms: {{ $invoice->shipment_terms }}<br>
                Courier Name: {{ $invoice->courier_name }}<br>
                Tracking Number: {{ $invoice->tracking_number }}<br>
            </td>
            <td style="width:50%; vertical-align:top;">
                <strong>Receiver :</strong><br><br>
                {!! nl2br(e($invoice->customer_address_block)) !!}
            </td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th>Art Num</th>
                <th>ARTICLE DESCRIPTION</th>
                <th>SIZE</th>
                <th>HS CODE</th>
                <th>QTY</th>
                <th>UNIT PRICE</th>
                <th>SUB TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->art_num }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->size }}</td>
                    <td>{{ $item->hs_code }}</td>
                    <td style="text-align:right">{{ $item->qty }}</td>
                    <td style="text-align:right">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align:right">{{ number_format($item->sub_total, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="6" style="text-align:right;"><strong>Total :</strong></td>
                <td style="text-align:right"><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <div>
        {!! nl2br(e($invoice->footer_note)) !!}
    </div>
</body>

</html>
