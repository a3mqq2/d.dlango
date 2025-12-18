<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.print_barcode') }} - {{ $item['name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
        .barcode-container {
            display: flex;
            flex-wrap: wrap;
            padding: 10px;
            gap: 10px;
        }
        .barcode-item {
            width: 80mm;
            height: 50mm;
            border: 1px dashed #ccc;
            padding: 3mm;
            text-align: center;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .barcode-item .product-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2mm;
            overflow: hidden;
            line-height: 1.2;
        }
        .barcode-item .barcode {
            margin: 2mm 0;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode-item .barcode svg {
            width: 70mm;
            height: 20mm;
        }
        .barcode-item .product-code {
            font-size: 14px;
            font-weight: bold;
            font-family: monospace;
            margin: 5px 0;
        }
        .barcode-item .product-price {
            font-size: 14px;
            font-weight: bold;
            color: #28a745;
        }
        .no-print {
            text-align: center;
            padding: 20px;
        }
        @media print {
            .no-print { display: none; }
            body {
                margin: 0;
                padding: 0;
            }
            .barcode-container {
                padding: 0;
                gap: 0;
            }
            .barcode-item {
                width: 80mm;
                height: 50mm;
                border: 1px solid #000;
                margin: 0;
            }
            @page {
                size: 80mm 50mm;
                margin: 0;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 16px; cursor: pointer; margin-bottom: 20px;">
            {{ __('messages.print') }}
        </button>
    </div>

    <div class="barcode-container">
        @for($i = 0; $i < $quantity; $i++)
            <div class="barcode-item">
                <div class="product-name">{{ $item['name'] }}</div>
                <div class="barcode">
                    <svg id="barcode-{{ $i }}"></svg>
                </div>
                <div class="product-code">{{ $item['code'] }}</div>
                <div class="product-price">{{ number_format($item['price'], 2) }} {{ __('messages.currency') }}</div>
            </div>
        @endfor
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @for($i = 0; $i < $quantity; $i++)
                JsBarcode("#barcode-{{ $i }}", "{{ $item['code'] }}", {
                    format: "CODE128",
                    width: 2,
                    height: 50,
                    displayValue: false,
                    margin: 0
                });
            @endfor

            // Auto print
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
