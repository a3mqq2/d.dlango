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
        }

        /* ملصق بالعرض: 50mm عرض × 30mm ارتفاع */
        .label {
            width: 50mm;
            height: 30mm;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            page-break-after: always;
            overflow: hidden;
        }
        .label:last-child {
            page-break-after: auto;
        }

        .label .product-name {
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            width: 100%;
        }

        .label .barcode {
            width: 45mm;
            height: 15mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .label .barcode svg {
            width: 45mm;
            height: 15mm;
        }

        .label .product-code {
            font-size: 10px;
            text-align: center;
            font-family: monospace;
            font-weight: bold;
        }

        .label .product-price {
            font-size: 12px;
            text-align: center;
            font-weight: bold;
        }

        .no-print {
            text-align: center;
            padding: 15px;
            background: #f0f0f0;
            margin-bottom: 10px;
        }
        .no-print button {
            padding: 10px 25px;
            font-size: 14px;
            cursor: pointer;
            margin: 5px;
            border: none;
            border-radius: 5px;
        }
        .btn-print { background: #007bff; color: white; }
        .btn-back { background: #6c757d; color: white; }

        .summary {
            text-align: center;
            padding: 10px;
            background: #e9ecef;
            font-size: 14px;
        }

        @media print {
            .no-print, .summary { display: none; }
            @page {
                size: 50mm 30mm;
                margin: 0;
            }
            .label {
                width: 50mm;
                height: 30mm;
            }
        }

        @media screen {
            .label {
                border: 1px solid #000;
                margin: 10px auto;
                background: white;
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
        {{ __('messages.total_barcodes') }}: <strong>{{ count($barcodes) }}</strong>
        &nbsp;|&nbsp;
        {{ __('messages.label_size') }}: <strong>50mm × 30mm</strong>
    </div>

    @foreach($barcodes as $index => $item)
    <div class="label">
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
                width: 2,
                height: 50,
                displayValue: false,
                margin: 0
            });
            @endforeach
        });
    </script>
</body>
</html>
