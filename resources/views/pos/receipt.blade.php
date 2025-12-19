<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ __('messages.receipt') }} - {{ $sale->invoice_number }}</title>

<style>
*{margin:0;padding:0;box-sizing:border-box}

body{
font-family:Courier New,monospace;
font-size:14px;
font-weight:bold;
background:#fff;
color:#000
}

.receipt{
width:76mm;
margin:0 auto;
padding:6px;
background:#fff
}

.header{
text-align:center;
border-bottom:2px dashed #000;
padding-bottom:6px;
margin-bottom:8px
}

.header img{
width:50mm;
display:block;
margin:0 auto 4px
}

.info{
margin-bottom:8px;
font-size:13px
}

.info table{
width:100%;
border-collapse:collapse
}

.info td{
padding:2px 0
}

.info .label{
width:45%;
font-weight:bold
}

.info .value{
width:55%;
text-align:left
}

html[dir="rtl"] .info .value{
text-align:right
}

.items{
width:100%;
border-collapse:collapse;
font-size:13px;
margin-bottom:8px
}

.items th{
border-top:2px solid #000;
border-bottom:2px solid #000;
padding:4px 0;
text-align:center
}

.items td{
padding:4px 0;
text-align:center
}

.items td:first-child{
text-align:right
}

.items tr{
border-bottom:1px dashed #000
}

.totals{
border-top:2px dashed #000;
padding-top:6px;
margin-top:6px;
font-size:14px
}

.totals table{
width:100%;
border-collapse:collapse
}

.totals td{
padding:3px 0
}

.totals .label{
text-align:right
}

.totals .value{
text-align:left
}

html[dir="rtl"] .totals .value{
text-align:right
}

.grand{
font-size:17px;
border-top:2px solid #000;
padding-top:4px
}

.footer{
text-align:center;
border-top:2px dashed #000;
margin-top:8px;
padding-top:6px;
font-size:13px
}

.barcode{
text-align:center;
margin-top:6px
}

.barcode img{
width:62mm
}

.brand{
text-align:center;
margin-top:6px;
font-size:12px
}

@page{
size:80mm auto;
margin:0
}
</style>
</head>

<body onload="window.print()">

@php
$logoBase64=null;
$logoPath=public_path('logo-dark.png');
if(file_exists($logoPath)){
$logoBase64='data:image/png;base64,'.base64_encode(file_get_contents($logoPath));
}
$barcodeBase64='data:image/png;base64,'.DNS1D::getBarcodePNG($sale->invoice_number,'C128',2.2,65);
@endphp

<div class="receipt">

<div class="header">
@if($logoBase64)
<img src="{{ $logoBase64 }}">
@endif
<p>{{ __('messages.sales_receipt') }}</p>
</div>

<div class="info">
<table>
<tr>
<td class="label">{{ __('messages.invoice_number') }}</td>
<td class="value">{{ $sale->invoice_number }}</td>
</tr>
<tr>
<td class="label">{{ __('messages.date') }}</td>
<td class="value">{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
</tr>
<tr>
<td class="label">{{ __('messages.customer') }}</td>
<td class="value">{{ $sale->customer->name }}</td>
</tr>
<tr>
<td class="label">{{ __('messages.cashier') }}</td>
<td class="value">{{ $sale->user->name }}</td>
</tr>
</table>
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
<td>{{ $item->quantity }}</td>
<td>{{ number_format($item->unit_price,2) }}</td>
<td>{{ number_format($item->subtotal,2) }}</td>
</tr>
@endforeach
</tbody>
</table>

<div class="totals">
<table>
<tr>
<td class="label">{{ __('messages.subtotal') }}</td>
<td class="value">{{ number_format($sale->subtotal,2) }}</td>
</tr>
@if($sale->discount>0)
<tr>
<td class="label">{{ __('messages.discount') }}</td>
<td class="value">-{{ number_format($sale->discount,2) }}</td>
</tr>
@endif
<tr class="grand">
<td class="label">{{ __('messages.total') }}</td>
<td class="value">{{ number_format($sale->total_amount,2) }}</td>
</tr>
</table>
</div>

<div class="footer">
<p>{{ __('messages.thank_you') }}</p>
<p>{{ __('messages.visit_again') }}</p>
</div>

<div class="barcode">
<img src="{{ $barcodeBase64 }}">
</div>

<div class="brand">
شركة حلول لتقنية المعلومات HULUL-EPOS
</div>

</div>

</body>
</html>
