<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales Invoice {{ $invoice->invoice_no }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 16px;
        }

        .page-break {
            page-break-after: always;
        }

        /* Simple mapping for some of the utility classes used in _preview_card */
        .border {
            border: 1px solid #d4d4d4;
        }
    </style>
</head>

<body>
    @include('sales_invoices._preview_card', ['invoice' => $invoice])
</body>

</html>
