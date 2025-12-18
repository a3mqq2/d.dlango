<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.print_barcode') }} - {{ $item['name'] }}</title>
    <style>
        * {
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            font-family: Arial, sans-serif;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
        .barcode-item {
            width: 38mm;
            height: 25mm;
            padding: 1mm !important;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-after: always;
            overflow: hidden;
        }
        .barcode-item:last-child {
            page-break-after: auto;
        }
        .barcode-item .product-name {
            font-size: 6px;
            font-weight: bold;
            line-height: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin: 0 !important;
            padding: 0 !important;
        }
        .barcode-item .barcode {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
            padding: 0 !important;
        }
        .barcode-item .barcode svg {
            width: 34mm;
            height: 12mm;
        }
        .barcode-item .product-code {
            font-size: 6px;
            font-weight: bold;
            font-family: monospace;
            margin: 0 !important;
            padding: 0 !important;
        }
        .barcode-item .product-price {
            font-size: 7px;
            font-weight: bold;
            color: #000;
            margin: 0 !important;
            padding: 0 !important;
        }
        .no-print {
            text-align: center;
            padding: 20px !important;
        }
        .barcode-container {
            padding: 0 !important;
            margin: 0 !important;
        }
        @media print {
            .no-print { display: none !important; }
            html, body {
                width: 38mm !important;
                height: 25mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .barcode-container {
                padding: 0 !important;
                margin: 0 !important;
            }
            .barcode-item {
                width: 38mm !important;
                height: 25mm !important;
                margin: 0 !important;
                padding: 1mm !important;
                border: none !important;
            }
            @page {
                size: 25mm 38mm;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
        @media screen {
            .barcode-item {
                border: 1px dashed #ccc;
                margin: 5px auto !important;
                background: white;
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
        <p style="margin-top: 10px; color: #666;">{{ __('messages.label_size') }}: 38mm x 25mm</p>
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
                    width: 1,
                    height: 30,
                    displayValue: false,
                    margin: 0
                });
            @endfor

            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
