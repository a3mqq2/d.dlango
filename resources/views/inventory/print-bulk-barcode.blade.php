<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.print_barcode') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
        .barcode-page {
            width: 38mm;
            height: 25mm;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-after: always;
            overflow: hidden;
        }
        .barcode-page:last-child {
            page-break-after: auto;
        }
        .product-name {
            height: 6mm;
            font-size: 7px;
            font-weight: bold;
            line-height: 1;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .barcode {
            height: 11mm;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode svg {
            width: 30mm;
            height: auto;
        }
        .product-code {
            height: 4mm;
            font-size: 8px;
            font-family: monospace;
            font-weight: bold;
        }
        .product-price {
            height: 4mm;
            font-size: 9px;
            font-weight: bold;
        }
        .no-print {
            text-align: center;
            padding: 15px;
            background: #f5f5f5;
        }
        .no-print button {
            padding: 8px 20px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            margin: 0 5px;
        }
        .btn-print {
            background: #007bff;
            color: #fff;
        }
        .btn-back {
            background: #6c757d;
            color: #fff;
        }
        .summary {
            text-align: center;
            padding: 8px;
            font-size: 13px;
            background: #e9ecef;
        }
        @media print {
            .no-print, .summary {
                display: none;
            }
            @page {
                size: 38mm 25mm;
                margin: 0;
            }
            .barcode-page {
                width: 38mm;
                height: 25mm;
            }
        }
        @media screen {
            .barcode-page {
                border: 1px dashed #ccc;
                margin: 10px auto;
                background: #fff;
            }
        }
    </style>
    <script src="{{ asset('assets/vendor/jsbarcode/JsBarcode.all.min.js') }}"></script>
</head>
<body>

<div class="no-print">
    <button class="btn-back" onclick="window.history.back()">{{ __('messages.back') }}</button>
    <button class="btn-print" onclick="window.print()">{{ __('messages.print') }}</button>
</div>

<div class="summary">
    {{ __('messages.total_barcodes') }}:
    <strong>{{ count($barcodes) }}</strong>
    |
    {{ __('messages.label_size') }}:
    <strong>38mm × 25mm</strong>
</div>

@foreach($barcodes as $index => $item)
    <div class="barcode-page">
        <div class="product-name">{{ $item['name'] }}</div>
        <div class="barcode">
            <svg id="barcode-{{ $index }}"></svg>
        </div>
        <div class="product-code">{{ $item['code'] }}</div>
        <div class="product-price">{{ number_format($item['price'], 2) }} {{ __('messages.currency') }}</div>
    </div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
@foreach($barcodes as $index => $item)
    JsBarcode("#barcode-{{ $index }}", "{{ $item['code'] }}", {
        format: "CODE128",
        width: 0.9,
        height: 22,
        displayValue: false,
        margin: 0
    });
@endforeach
});
</script>

</body>
</html>