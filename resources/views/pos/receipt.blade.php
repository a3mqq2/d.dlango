<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.receipt') }} - {{ $sale->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            background: white;
        }
        .receipt {
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 11px;
            color: #333;
        }
        .info {
            margin-bottom: 15px;
            font-size: 11px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items th, .items td {
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            padding: 5px 2px;
            font-size: 11px;
        }
        .items th {
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
        }
        .items .price, .items .qty, .items .total {
            text-align: center;
        }
        .items tbody tr {
            border-bottom: 1px dashed #ccc;
        }
        .totals {
            margin-top: 10px;
            border-top: 2px dashed #000;
            padding-top: 10px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .totals-row.grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .payment-info {
            margin-top: 15px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #000;
            font-size: 11px;
        }
        .footer p {
            margin-bottom: 5px;
        }
        .barcode {
            text-align: center;
            margin-top: 15px;
        }
        .barcode svg {
            max-width: 100%;
        }
        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 5px;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="receipt">
        {{-- Header --}}
        <div class="header">
            <img src="{{asset('logo.png')}}" width="200" alt="">
            <p>{{ __('messages.sales_receipt') }}</p>
        </div>

        {{-- Invoice Info --}}
        <div class="info">
            <div class="info-row">
                <span>{{ __('messages.invoice_number') }}:</span>
                <span>{{ $sale->invoice_number }}</span>
            </div>
            <div class="info-row">
                <span>{{ __('messages.date') }}:</span>
                <span>{{ $sale->sale_date->format('Y-m-d H:i') }}</span>
            </div>
            <div class="info-row">
                <span>{{ __('messages.customer') }}:</span>
                <span>{{ $sale->customer->name }}</span>
            </div>
            <div class="info-row">
                <span>{{ __('messages.cashier') }}:</span>
                <span>{{ $sale->user->name }}</span>
            </div>
        </div>

        {{-- Items --}}
        <table class="items">
            <thead>
                <tr>
                    <th>{{ __('messages.item') }}</th>
                    <th class="qty">{{ __('messages.qty') }}</th>
                    <th class="price">{{ __('messages.price') }}</th>
                    <th class="total">{{ __('messages.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->display_name }}</td>
                    <td class="qty">{{ $item->quantity }}</td>
                    <td class="price">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="total">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals">
            <div class="totals-row">
                <span>{{ __('messages.subtotal') }}:</span>
                <span>{{ number_format($sale->subtotal, 2) }} {{ __('messages.currency') }}</span>
            </div>
            @if($sale->discount > 0)
            <div class="totals-row">
                <span>{{ __('messages.discount') }}:</span>
                <span>-{{ number_format($sale->discount, 2) }} {{ __('messages.currency') }}</span>
            </div>
            @endif
            <div class="totals-row grand-total">
                <span>{{ __('messages.total') }}:</span>
                <span>{{ number_format($sale->total_amount, 2) }} {{ __('messages.currency') }}</span>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="payment-info">
            <div class="totals-row">
                <span>{{ __('messages.payment_method') }}:</span>
                <span>{{ $sale->payment_method === 'cash' ? __('messages.cash') : __('messages.credit') }}</span>
            </div>
            <div class="totals-row">
                <span>{{ __('messages.paid') }}:</span>
                <span>{{ number_format($sale->paid_amount, 2) }} {{ __('messages.currency') }}</span>
            </div>
            @if($sale->remaining_amount > 0)
            <div class="totals-row">
                <span>{{ __('messages.remaining') }}:</span>
                <span>{{ number_format($sale->remaining_amount, 2) }} {{ __('messages.currency') }}</span>
            </div>
            @endif
            @if($sale->paid_amount > $sale->total_amount)
            <div class="totals-row">
                <span>{{ __('messages.change') }}:</span>
                <span>{{ number_format($sale->paid_amount - $sale->total_amount, 2) }} {{ __('messages.currency') }}</span>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>{{ __('messages.thank_you') }}</p>
            <p>{{ __('messages.visit_again') }}</p>
        </div>

        {{-- Barcode --}}
        <div class="barcode">
            <svg id="barcode"></svg>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcode", "{{ $sale->invoice_number }}", {
            format: "CODE128",
            width: 1.5,
            height: 40,
            displayValue: false
        });
    </script>
</body>
</html>
