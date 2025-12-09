<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.invoice') }} - {{ $sale->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            background: #f5f5f5;
            color: #333;
        }
        .invoice-container {
            min-height: 297mm;
            margin: 0 auto;
            background: white;
            padding: 20mm;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #b65f7a;
        }
        .company-info {
            flex: 1;
        }
        .company-info img {
            max-width: 180px;
            margin-bottom: 10px;
        }
        .company-info h1 {
            font-size: 24px;
            color: #b65f7a;
            margin-bottom: 5px;
        }
        .company-info p {
            color: #666;
            font-size: 12px;
        }
        .invoice-title {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
        }
        .invoice-title h2 {
            font-size: 28px;
            color: #b65f7a;
            margin-bottom: 10px;
        }
        .invoice-title .invoice-number {
            font-size: 16px;
            color: #666;
        }
        .invoice-title .invoice-date {
            font-size: 14px;
            color: #999;
            margin-top: 5px;
        }
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 40px;
        }
        .party-box {
            flex: 1;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 4px solid #b65f7a;
        }
        .party-box h3 {
            color: #b65f7a;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .party-box p {
            margin-bottom: 5px;
            font-size: 13px;
        }
        .party-box .name {
            font-weight: bold;
            font-size: 16px;
            color: #333;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #b65f7a;
            color: white;
            padding: 12px 15px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            font-weight: 600;
        }
        .items-table th:first-child {
            border-radius: {{ app()->getLocale() == 'ar' ? '0 8px 0 0' : '8px 0 0 0' }};
        }
        .items-table th:last-child {
            border-radius: {{ app()->getLocale() == 'ar' ? '8px 0 0 0' : '0 8px 0 0' }};
        }
        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .items-table tbody tr:hover {
            background: #f8f9fa;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-table .text-end {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
        }
        .items-table tfoot td {
            padding: 10px 15px;
            font-weight: bold;
        }
        .items-table tfoot tr:first-child td {
            border-top: 2px solid #b65f7a;
        }
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .totals-box {
            width: 300px;
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        .totals-row:last-child {
            border-bottom: none;
        }
        .totals-row.grand-total {
            background: #b65f7a;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .totals-row .label {
            color: #666;
        }
        .totals-row.grand-total .label {
            color: white;
        }
        .payment-section {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .payment-box {
            flex: 1;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .payment-box.method {
            background: #e8f5e9;
            border: 1px solid #4caf50;
        }
        .payment-box.paid {
            background: #e3f2fd;
            border: 1px solid #2196f3;
        }
        .payment-box.remaining {
            background: #ffebee;
            border: 1px solid #f44336;
        }
        .payment-box .value {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .payment-box .label {
            font-size: 12px;
            color: #666;
        }
        .notes-section {
            background: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #ffc107;
        }
        .notes-section h4 {
            color: #856404;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 12px;
        }
        .footer p {
            margin-bottom: 5px;
        }
        .barcode {
            text-align: center;
            margin-top: 20px;
        }
        @media print {
            body {
                background: white;
            }
            .invoice-container {
                padding: 10mm;
                margin: 0;
            }
            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="invoice-container">
        {{-- Header --}}
        <div class="invoice-header">
            <div class="company-info">
                <img src="{{ asset('logo.png') }}" alt="Logo">
                <p>{{ __('messages.sales_invoice') }}</p>
            </div>
            <div class="invoice-title">
                <h2>{{ __('messages.invoice') }}</h2>
                <div class="invoice-number">#{{ $sale->invoice_number }}</div>
                <div class="invoice-date">{{ $sale->sale_date->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        {{-- Parties --}}
        <div class="parties">
            <div class="party-box">
                <h3>{{ __('messages.customer') }}</h3>
                <p class="name">{{ $sale->customer->name }}</p>
                @if($sale->customer->phone)
                <p><i class="ti ti-phone"></i> {{ $sale->customer->phone }}</p>
                @endif
            </div>
            <div class="party-box">
                <h3>{{ __('messages.cashier') }}</h3>
                <p class="name">{{ $sale->user->name }}</p>
            </div>
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>{{ __('messages.product') }}</th>
                    <th class="text-center" style="width: 80px;">{{ __('messages.quantity') }}</th>
                    <th class="text-end" style="width: 120px;">{{ __('messages.unit_price') }}</th>
                    <th class="text-end" style="width: 100px;">{{ __('messages.discount') }}</th>
                    <th class="text-end" style="width: 120px;">{{ __('messages.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->variant)
                        <br><small style="color: #666;">{{ $item->variant->variant_name }}</small>
                        @endif
                        <br><small style="color: #999;">{{ $item->variant ? $item->variant->code : $item->product->code }}</small>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->discount, 2) }}</td>
                    <td class="text-end"><strong>{{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-section">
            <div class="totals-box">
                <div class="totals-row">
                    <span class="label">{{ __('messages.subtotal') }}</span>
                    <span>{{ number_format($sale->subtotal, 2) }} {{ __('messages.currency') }}</span>
                </div>
                @if($sale->discount > 0)
                <div class="totals-row">
                    <span class="label">{{ __('messages.discount') }}</span>
                    <span style="color: #f44336;">-{{ number_format($sale->discount, 2) }} {{ __('messages.currency') }}</span>
                </div>
                @endif
                <div class="totals-row grand-total">
                    <span class="label">{{ __('messages.total') }}</span>
                    <span>{{ number_format($sale->total_amount, 2) }} {{ __('messages.currency') }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="payment-section">
            <div class="payment-box method">
                <div class="value">
                    @if($sale->payment_method === 'cash')
                        <span style="color: #4caf50;">{{ __('messages.cash') }}</span>
                    @else
                        <span style="color: #ff9800;">{{ __('messages.credit') }}</span>
                    @endif
                </div>
                <div class="label">{{ __('messages.payment_method') }}</div>
            </div>
            <div class="payment-box paid">
                <div class="value" style="color: #2196f3;">{{ number_format($sale->paid_amount, 2) }} {{ __('messages.currency') }}</div>
                <div class="label">{{ __('messages.paid') }}</div>
            </div>
            @if($sale->remaining_amount > 0)
            <div class="payment-box remaining">
                <div class="value" style="color: #f44336;">{{ number_format($sale->remaining_amount, 2) }} {{ __('messages.currency') }}</div>
                <div class="label">{{ __('messages.remaining') }}</div>
            </div>
            @endif
        </div>

        {{-- Notes --}}
        @if($sale->notes)
        <div class="notes-section">
            <h4>{{ __('messages.notes') }}</h4>
            <p>{{ $sale->notes }}</p>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p>{{ __('messages.thank_you') }}</p>
            <p>{{ __('messages.visit_again') }}</p>
            <div class="barcode">
                <svg id="barcode"></svg>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcode", "{{ $sale->invoice_number }}", {
            format: "CODE128",
            width: 2,
            height: 50,
            displayValue: true,
            fontSize: 14
        });
    </script>
</body>
</html>
