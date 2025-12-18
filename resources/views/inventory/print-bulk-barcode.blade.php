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
            width: 25mm;
            height: 38mm;
            padding: 2mm;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: center;
            page-break-after: always;
            margin: 0 auto;
            overflow: hidden;
        }
        .barcode-page:last-child {
            page-break-after: auto;
        }
        .barcode-page .product-name {
            font-size: 7px;
            font-weight: bold;
            line-height: 1.1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
        }
        .barcode-page .barcode {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        .barcode-page .barcode svg {
            width: 22mm;
            height: 18mm;
        }
        .barcode-page .product-code {
            font-size: 8px;
            font-weight: bold;
            font-family: monospace;
        }
        .barcode-page .product-price {
            font-size: 9px;
            font-weight: bold;
            color: #000;
        }
        .no-print {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }
        .no-print button {
            padding: 10px 30px;
            font-size: 16px;
            cursor: pointer;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
        }
        .no-print .btn-print {
            background: #007bff;
            color: white;
        }
        .no-print .btn-back {
            background: #6c757d;
            color: white;
        }
        .summary {
            text-align: center;
            padding: 10px;
            background: #e9ecef;
            font-size: 14px;
        }
        @media print {
            .no-print, .summary { display: none; }
            @page {
                size: 25mm 38mm;
                margin: 0;
            }
            .barcode-page {
                width: 25mm;
                height: 38mm;
            }
        }
        @media screen {
            .barcode-page {
                border: 1px dashed #ccc;
                margin: 10px auto;
                background: white;
            }
        }
    </style>
    <script src="{{ asset('assets/vendor/jsbarcode/JsBarcode.all.min.js') }}"></script>
</head>
<body>
    <div class="no-print">
        <button class="btn-back" onclick="window.history.back()">
            {{ __('messages.back') }}
        </button>
        <button class="btn-print" onclick="window.print()">
            {{ __('messages.print') }}
        </button>
    </div>

    <div class="summary">
        {{ __('messages.total_barcodes') }}: <strong>{{ count($barcodes) }}</strong>
        &nbsp;|&nbsp;
        {{ __('messages.label_size') }}: <strong>38mm x 25mm (landscape)</strong>
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
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($barcodes as $index => $item)
                JsBarcode("#barcode-{{ $index }}", "{{ $item['code'] }}", {
                    format: "CODE128",
                    width: 1.2,
                    height: 45,
                    displayValue: false,
                    margin: 0
                });
            @endforeach
        });
    </script>
</body>
</html>
