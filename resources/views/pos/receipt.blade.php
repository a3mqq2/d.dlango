<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('messages.receipt') }} - {{ $sale->invoice_number }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:Courier New,monospace;font-size:12px;width:80mm;margin:0 auto;padding:6px;background:#fff}
.header{text-align:center;border-bottom:2px dashed #000;padding-bottom:8px;margin-bottom:10px}
.header img{max-width:55mm;margin:0 auto 5px;display:block}
.info{font-size:11px;margin-bottom:10px}
.info-row{display:flex;justify-content:space-between;margin-bottom:2px}
.items{width:100%;border-collapse:collapse;font-size:11px;margin-bottom:10px}
.items th{border-top:1px solid #000;border-bottom:1px solid #000;padding:3px 0}
.items td{padding:3px 0}
.items td.qty,.items td.price,.items td.total{text-align:center}
.items tr{border-bottom:1px dashed #ccc}
.totals{border-top:2px dashed #000;padding-top:6px;margin-top:6px}
.totals-row{display:flex;justify-content:space-between;margin-bottom:3px}
.grand{font-size:15px;font-weight:bold;border-top:1px solid #000;padding-top:4px}
.footer{text-align:center;border-top:2px dashed #000;margin-top:10px;padding-top:6px;font-size:11px}
.barcode{text-align:center;margin-top:8px}
.barcode img{max-width:65mm}
@page{size:80mm auto;margin:0}
</style>
</head>
<body onload="window.print()">

@php
$logoBase64=null;
$logoPath=public_path('logo.png');
if(file_exists($logoPath)){
$logoBase64='data:image/png;base64,'.base64_encode(file_get_contents($logoPath));
}

$barcodePng = DNS1D::getBarcodePNG($sale->invoice_number,'C128',2,60);
$barcodeBase64 = 'data:image/png;base64,'.$barcodePng;
@endphp

<div class="header">
@if($logoBase64)
<img src="{{ $logoBase64 }}">
@endif
<p>{{ __('messages.sales_receipt') }}</p>
</div>

<div class="info">
<div class="info-row"><span>{{ __('messages.invoice_number') }}</span><span>{{ $sale->invoice_number }}</span></div>
<div class="info-row"><span>{{ __('messages.date') }}</span><span>{{ $sale->sale_date->format('Y-m-d H:i') }}</span></div>
<div class="info-row"><span>{{ __('messages.customer') }}</span><span>{{ $sale->customer->name }}</span></div>
<div class="info-row"><span>{{ __('messages.cashier') }}</span><span>{{ $sale->user->name }}</span></div>
</div>

<table class="items">
<thead>
<tr>
<th>{{ __('messages.item') }}</th>
<th>{{ __('messages.qty') }}</th>
<th>{{ __('messages.price') }}</th>
<th>{{ __('messages.total') }}</th>
</tr>
</thead>
<tbody>
@foreach($sale->items as $item)
<tr>
<td>{{ $item->display_name }}</td>
<td class="qty">{{ $item->quantity }}</td>
<td class="price">{{ number_format($item->unit_price,2) }}</td>
<td class="total">{{ number_format($item->subtotal,2) }}</td>
</tr>
@endforeach
</tbody>
</table>

<div class="totals">
<div class="totals-row"><span>{{ __('messages.subtotal') }}</span><span>{{ number_format($sale->subtotal,2) }}</span></div>
@if($sale->discount>0)
<div class="totals-row"><span>{{ __('messages.discount') }}</span><span>-{{ number_format($sale->discount,2) }}</span></div>
@endif
<div class="totals-row grand"><span>{{ __('messages.total') }}</span><span>{{ number_format($sale->total_amount,2) }}</span></div>
</div>

<div class="footer">
<p>{{ __('messages.thank_you') }}</p>
<p>{{ __('messages.visit_again') }}</p>
</div>

<div class="barcode">
<img src="{{ $barcodeBase64 }}">
</div>

</body>
</html>