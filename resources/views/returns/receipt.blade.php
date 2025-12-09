<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.return_receipt') }} - {{ $return->return_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            background: white;
            padding: 5mm;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }
        .header img {
            max-width: 120px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16px;
            margin-bottom: 3px;
            color: #b65f7a;
        }
        .header .return-badge {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 12px;
            margin-top: 5px;
        }
        .info-section {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .info-row .label {
            color: #666;
        }
        .items-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .items-table th {
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            padding: 5px 0;
            border-bottom: 1px solid #333;
            font-size: 11px;
        }
        .items-table td {
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
            font-size: 11px;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-end {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
        }
        .totals-section {
            border-top: 1px dashed #333;
            padding-top: 10px;
            margin-bottom: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-row.grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #dc3545;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 5px;
        }
        .refund-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
        }
        .refund-info .method {
            font-size: 14px;
            font-weight: bold;
            color: #b65f7a;
        }
        .footer {
            text-align: center;
            padding-top: 10px;
            border-top: 1px dashed #333;
            color: #666;
            font-size: 11px;
        }
        .barcode {
            text-align: center;
            margin-top: 10px;
        }
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 3mm;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print();">
    {{-- Header --}}
    <div class="header">
        <img src="{{ asset('logo.png') }}" alt="Logo">
        <h2>{{ __('messages.app_name') }}</h2>
        <div class="return-badge">{{ __('messages.return_receipt') }}</div>
    </div>

    {{-- Return Info --}}
    <div class="info-section">
        <div class="info-row">
            <span class="label">{{ __('messages.return_number') }}:</span>
            <span><strong>{{ $return->return_number }}</strong></span>
        </div>
        <div class="info-row">
            <span class="label">{{ __('messages.original_invoice') }}:</span>
            <span>{{ $return->sale->invoice_number }}</span>
        </div>
        <div class="info-row">
            <span class="label">{{ __('messages.return_date') }}:</span>
            <span>{{ $return->return_date->format('Y-m-d H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="label">{{ __('messages.customer') }}:</span>
            <span>{{ $return->customer->name }}</span>
        </div>
        <div class="info-row">
            <span class="label">{{ __('messages.cashier') }}:</span>
            <span>{{ $return->user->name }}</span>
        </div>
    </div>

    {{-- Reason --}}
    @if($return->reason)
    <div class="info-section">
        <div class="info-row">
            <span class="label">{{ __('messages.reason') }}:</span>
            <span>{{ $return->reason }}</span>
        </div>
    </div>
    @endif

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>{{ __('messages.product') }}</th>
                <th class="text-center">{{ __('messages.qty') }}</th>
                <th class="text-end">{{ __('messages.price') }}</th>
                <th class="text-end">{{ __('messages.total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->items as $item)
            <tr>
                <td>
                    {{ $item->product->name }}
                    @if($item->variant)
                        <br><small>{{ $item->variant->variant_name }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-section">
        <div class="total-row grand-total">
            <span>{{ __('messages.return_total') }}:</span>
            <span>{{ number_format($return->total_amount, 2) }} {{ __('messages.currency') }}</span>
        </div>
    </div>

    {{-- Refund Info --}}
    <div class="refund-info">
        <div class="method">
            @if($return->refund_method === 'cash')
                {{ __('messages.cash_refund') }}
            @else
                {{ __('messages.credit_refund') }}
            @endif
        </div>
        @if($return->refund_method === 'cash' && $return->cashbox)
            <small>{{ $return->cashbox->name }}</small>
        @endif
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ __('messages.thank_you') }}</p>
        <p>{{ __('messages.visit_again') }}</p>
        <div class="barcode">
            <svg id="barcode"></svg>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcode", "{{ $return->return_number }}", {
            format: "CODE128",
            width: 1.5,
            height: 40,
            displayValue: true,
            fontSize: 10
        });
    </script>
</body>
</html>
