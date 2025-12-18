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

        /* ملصق واحد في كل صفحة 150mm عرض × 100mm ارتفاع */
        .label {
            width: 150mm;
            height: 100mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 5mm;
            page-break-after: always;
            overflow: hidden;
        }
        .label:last-child {
            page-break-after: auto;
        }

        .label .product-name {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            width: 100%;
            margin-bottom: 5mm;
        }

        .label .barcode {
            width: 120mm;
            height: 50mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .label .barcode svg {
            width: 120mm;
            height: 50mm;
        }

        .label .product-code {
            font-size: 20px;
            text-align: center;
            font-family: monospace;
            font-weight: bold;
            margin-top: 3mm;
        }

        .label .product-price {
            font-size: 24px;
            text-align: center;
            font-weight: bold;
            margin-top: 2mm;
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
                size: 150mm 100mm landscape;
                margin: 0;
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
        {{ __('messages.label_size') }}: <strong>150mm × 100mm (landscape) - 1 label per page</strong>
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
                width: 3,
                height: 100,
                displayValue: false,
                margin: 0
            });
            @endforeach
        });
    </script>
</body>
</html>
