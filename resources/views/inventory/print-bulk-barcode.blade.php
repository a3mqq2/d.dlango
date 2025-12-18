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

/* ===== LABEL 38mm x 25mm ===== */
.label {
    width: 38mm;
    height: 25mm;
    position: relative;
    page-break-after: always;
    overflow: hidden;
}

.label:last-child {
    page-break-after: auto;
}

/* ===== FIXED POSITIONS ===== */
.product-name {
    position: absolute;
    top: 1mm;
    left: 1mm;
    width: 36mm;
    font-size: 7px;
    font-weight: bold;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
}

.barcode {
    position: absolute;
    top: 7mm;
    left: 4mm;
    width: 30mm;
    height: 10mm;
}

.product-code {
    position: absolute;
    top: 18mm;
    left: 0;
    width: 38mm;
    font-size: 8px;
    font-family: monospace;
    font-weight: bold;
    text-align: center;
}

.product-price {
    position: absolute;
    top: 21mm;
    left: 0;
    width: 38mm;
    font-size: 9px;
    font-weight: bold;
    text-align: center;
}

/* ===== PRINT SETTINGS ===== */
@media print {
    @page {
        size: 38mm 25mm;
        margin: 0;
    }
}
</style>

<script src="{{ asset('assets/vendor/jsbarcode/JsBarcode.all.min.js') }}"></script>
</head>

<body>

@foreach($barcodes as $i => $item)
<div class="label">
    <div class="product-name">{{ $item['name'] }}</div>
    <svg id="barcode{{ $i }}" class="barcode"></svg>
    <div class="product-code">{{ $item['code'] }}</div>
    <div class="product-price">
        {{ number_format($item['price'], 2) }} {{ __('messages.currency') }}
    </div>
</div>
@endforeach

<script>
document.addEventListener('DOMContentLoaded', function () {
@foreach($barcodes as $i => $item)
    JsBarcode("#barcode{{ $i }}", "{{ $item['code'] }}", {
        format: "CODE128",
        width: 0.9,
        height: 18,
        displayValue: false,
        margin: 0
    });
@endforeach
});
</script>

</body>
</html>