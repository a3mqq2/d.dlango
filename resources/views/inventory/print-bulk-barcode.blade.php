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

        /* صفحة 150mm عرض × 100mm ارتفاع (landscape) */
        /* شبكة 3 أعمدة × 3 صفوف = 9 ملصقات */
        .print-page {
            width: 150mm;
            height: 100mm;
            padding: 2mm;
            display: flex;
            flex-wrap: wrap;
            align-content: flex-start;
            justify-content: space-around;
            page-break-after: always;
        }
        .print-page:last-child {
            page-break-after: auto;
        }

        .label {
            width: 48mm;
            height: 31mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2mm;
            overflow: hidden;
        }

        .label .product-name {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            width: 100%;
            margin-bottom: 1mm;
        }

        .label .barcode {
            width: 44mm;
            height: 16mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .label .barcode svg {
            width: 44mm;
            height: 16mm;
        }

        .label .product-code {
            font-size: 9px;
            text-align: center;
            font-family: monospace;
            font-weight: bold;
            margin-top: 1mm;
        }

        .label .product-price {
            font-size: 10px;
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
                size: 150mm 100mm landscape;
                margin: 0;
            }
            .label {
                border: none;
            }
        }

        @media screen {
            .print-page {
                border: 1px solid #000;
                margin: 10px auto;
                background: white;
            }
            .label {
                border: 1px dashed #ccc;
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
        {{ __('messages.label_size') }}: <strong>150mm × 100mm (landscape) - 9 labels per page</strong>
    </div>

    @php
        $labelsPerPage = 9; // 3 columns × 3 rows
        $totalPages = ceil(count($barcodes) / $labelsPerPage);
    @endphp

    @for($page = 0; $page < $totalPages; $page++)
    <div class="print-page">
        @for($i = $page * $labelsPerPage; $i < min(($page + 1) * $labelsPerPage, count($barcodes)); $i++)
        <div class="label">
            <div class="product-name">{{ $barcodes[$i]['name'] }}</div>
            <div class="barcode">
                <svg id="barcode-{{ $i }}"></svg>
            </div>
            <div class="product-code">{{ $barcodes[$i]['code'] }}</div>
            <div class="product-price">{{ number_format($barcodes[$i]['price'], 2) }} {{ __('messages.currency') }}</div>
        </div>
        @endfor
    </div>
    @endfor

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($barcodes as $i => $item)
            JsBarcode("#barcode-{{ $i }}", "{{ $item['code'] }}", {
                format: "CODE128",
                width: 1.8,
                height: 40,
                displayValue: false,
                margin: 0
            });
            @endforeach
        });
    </script>
</body>
</html>
