<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<title>Print Barcode</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
}

/* صفحة 100mm × 150mm تحتوي على شبكة من الملصقات */
/* 2 أعمدة × 5 صفوف = 10 ملصقات في الصفحة */
/* كل ملصق: 48mm عرض × 28mm ارتفاع (مع مسافات) */

.print-page {
    width: 100mm;
    height: 150mm;
    padding: 3mm;
    display: flex;
    flex-wrap: wrap;
    align-content: flex-start;
    page-break-after: always;
}

.print-page:last-child {
    page-break-after: auto;
}

.label {
    width: 47mm;
    height: 28mm;
    border: 0.5px dashed #ccc;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 1mm;
    overflow: hidden;
}

.label .name {
    font-size: 8px;
    font-weight: bold;
    text-align: center;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    width: 100%;
    margin-bottom: 1mm;
}

.label .barcode {
    width: 40mm;
    height: 14mm;
    display: flex;
    align-items: center;
    justify-content: center;
}

.label .barcode svg {
    width: 40mm;
    height: 14mm;
}

.label .code {
    font-size: 8px;
    text-align: center;
    font-family: monospace;
    font-weight: bold;
    margin-top: 1mm;
}

.label .price {
    font-size: 9px;
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

@media print {
    .no-print { display: none; }

    @page {
        size: 100mm 150mm;
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
}
</style>

<script src="{{ asset('assets/vendor/jsbarcode/JsBarcode.all.min.js') }}"></script>
</head>

<body>

<div class="no-print">
    <button class="btn-back" onclick="window.history.back()">{{ __('messages.back') }}</button>
    <button class="btn-print" onclick="window.print()">{{ __('messages.print') }}</button>
    <p style="margin-top: 10px; color: #666;">
        {{ __('messages.total_barcodes') }}: <strong>{{ count($barcodes) }}</strong> |
        {{ __('messages.label_size') }}: 100mm × 150mm (10 labels per page)
    </p>
</div>

@php
    $labelsPerPage = 10; // 2 columns × 5 rows
    $totalPages = ceil(count($barcodes) / $labelsPerPage);
@endphp

@for($page = 0; $page < $totalPages; $page++)
<div class="print-page">
    @for($i = $page * $labelsPerPage; $i < min(($page + 1) * $labelsPerPage, count($barcodes)); $i++)
    <div class="label">
        <div class="name">{{ $barcodes[$i]['name'] }}</div>
        <div class="barcode">
            <svg id="bc{{ $i }}"></svg>
        </div>
        <div class="code">{{ $barcodes[$i]['code'] }}</div>
        <div class="price">{{ number_format($barcodes[$i]['price'], 2) }} {{ __('messages.currency') }}</div>
    </div>
    @endfor
</div>
@endfor

<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($barcodes as $i => $item)
    JsBarcode("#bc{{ $i }}", "{{ $item['code'] }}", {
        format: "CODE128",
        width: 1.5,
        height: 35,
        displayValue: false,
        margin: 0
    });
    @endforeach
});
</script>

</body>
</html>
