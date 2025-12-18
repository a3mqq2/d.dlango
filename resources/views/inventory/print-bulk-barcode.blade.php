<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.print_barcode') }}</title>
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
        .barcode-page {
            width: 38mm;
            height: 25mm;
            padding: 0.5mm !important;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-after: always;
            overflow: hidden;
        }
        .barcode-page:last-child {
            page-break-after: auto;
        }
        .barcode-page .product-name {
            font-size: 8px;
            font-weight: bold;
            line-height: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin: 0 !important;
            padding: 0 !important;
        }
        .barcode-page .barcode {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 !important;
            padding: 0 !important;
        }
        .barcode-page .barcode svg {
            width: 36mm;
            height: 15mm;
        }
        .barcode-page .product-code {
            font-size: 8px;
            font-weight: bold;
            font-family: monospace;
            margin: 0 !important;
            padding: 0 !important;
        }
        .barcode-page .product-price {
            font-size: 9px;
            font-weight: bold;
            color: #000;
            margin: 0 !important;
            padding: 0 !important;
        }
        .no-print {
            text-align: center;
            padding: 20px !important;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }
        .no-print button {
            padding: 10px 30px !important;
            font-size: 16px;
            cursor: pointer;
            margin: 0 5px !important;
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
            padding: 10px !important;
            background: #e9ecef;
            font-size: 14px;
        }
        @media print {
            .no-print, .summary { display: none !important; }
            html, body {
                width: 38mm !important;
                height: 25mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .barcode-page {
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
            .barcode-page {
                border: 1px dashed #ccc;
                margin: 5px auto !important;
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
        <button class="btn-print" onclick="printBarcodes()">
            {{ __('messages.print') }}
        </button>
    </div>

    <div class="summary">
        {{ __('messages.total_barcodes') }}: <strong>{{ count($barcodes) }}</strong>
        &nbsp;|&nbsp;
        {{ __('messages.label_size') }}: <strong>38mm x 25mm</strong>
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
                    width: 1.5,
                    height: 40,
                    displayValue: false,
                    margin: 0
                });
            @endforeach
        });
    </script>

    <script>
function printBarcodes() {
    fetch('/dlango/public/inventory/print-barcodes-raw', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            barcodes: @json($barcodes)
        })
    })
    .then(r => r.json())
    .then(r => alert('Printed: ' + r.count))
    .catch(() => alert('Print failed'));
}
</script>



</body>
</html>
