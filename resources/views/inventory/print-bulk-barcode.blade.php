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

.page {
    width: 38mm;
    height: 25mm;
    position: relative;
    page-break-after: always;
}

.name {
    position: absolute;
    top: 7mm;
    left: 1mm;
    width: 36mm;
    height: 5mm;
    font-size: 7px;
    font-weight: bold;
    text-align: center;
    overflow: hidden;
    white-space: nowrap;
}

.barcode {
    position: absolute;
    top: 7mm;
    left: 4mm;
    width: 30mm;
    height: 10mm;
}

.code {
    position: absolute;
    top: 18mm;
    left: 0;
    width: 38mm;
    height: 3mm;
    font-size: 8px;
    text-align: center;
    font-family: monospace;
    font-weight: bold;
}

.price {
    position: absolute;
    top: 21mm;
    left: 0;
    width: 38mm;
    height: 3mm;
    font-size: 9px;
    text-align: center;
    font-weight: bold;
}

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
<div class="page">
    <div class="name">{{ $item['name'] }}</div>
    <svg id="bc{{ $i }}" class="barcode"></svg>
    <div class="code">{{ $item['code'] }}</div>
    <div class="price">{{ number_format($item['price'],2) }} {{ __('messages.currency') }}</div>
</div>
@endforeach

<script>
@foreach($barcodes as $i => $item)
JsBarcode("#bc{{ $i }}", "{{ $item['code'] }}", {
    format: "CODE128",
    width: 0.8,
    height: 18,
    displayValue: false,
    margin: 0
});
@endforeach
</script>

</body>
</html>
