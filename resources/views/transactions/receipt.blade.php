<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.receipt') }} #{{ $transaction->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: A5 landscape;
                margin: 10mm;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }

        .receipt-container {
            width: 210mm;
            height: 148mm;
            margin: 0 auto;
            background: white;
            padding: 20mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #b65f7a;
            padding-bottom: 20px;
        }

        .receipt-header h1 {
            color: #b65f7a;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .receipt-header h2 {
            color: #b65f7a;
            font-size: 18px;
            margin: 10px 0 0 0;
        }

        .receipt-number {
            position: absolute;
            top: 20mm;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 20mm;
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
        }

        .receipt-number span {
            display: block;
            color: #666;
            font-size: 12px;
        }

        .receipt-number strong {
            display: block;
            color: #b65f7a;
            font-size: 18px;
            margin-top: 5px;
        }

        .receipt-body {
            margin: 30px 0;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .receipt-row:nth-child(even) {
            background: #f8f9fa;
        }

        .receipt-row .label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .receipt-row .value {
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }

        .amount-box {
            background: {{ $transaction->type == 'deposit' ? '#28a745' : '#dc3545' }};
            color: white;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0;
        }

        .amount-box .type {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .amount-box .amount {
            font-size: 36px;
            font-weight: bold;
        }

        .amount-text {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
            font-style: italic;
            color: #666;
        }

        .receipt-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
            color: #999;
            font-size: 12px;
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .badge-type {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <button class="btn btn-primary btn-lg print-button no-print" onclick="window.print()">
        <i class="ti ti-printer"></i> {{ __('messages.print') }}
    </button>

    <div class="receipt-container">
        {{-- Receipt Header --}}
        <div class="receipt-header">
            <h1>{{ config('app.name', 'Hulul POS') }}</h1>
            <h2>{{ __('messages.payment_receipt') }}</h2>
        </div>

        {{-- Receipt Number --}}
        <div class="receipt-number">
            <span>{{ __('messages.receipt_number') }}</span>
            <strong dir="ltr">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</strong>
        </div>

        {{-- Receipt Body --}}
        <div class="receipt-body">
            {{-- Date & Time --}}
            <div class="receipt-row">
                <span class="label">{{ __('messages.date_time') }}</span>
                <span class="value" dir="ltr">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</span>
            </div>

            {{-- Cashbox --}}
            <div class="receipt-row">
                <span class="label">{{ __('messages.cashbox') }}</span>
                <span class="value">{{ $transaction->cashbox->name }}</span>
            </div>

            {{-- Recipient/Payer Name --}}
            <div class="receipt-row">
                <span class="label">{{ __('messages.recipient_payer_name') }}</span>
                <span class="value">{{ $transaction->recipient_name }}</span>
            </div>

            {{-- Recipient Number --}}
            @if($transaction->recipient_number)
                <div class="receipt-row">
                    <span class="label">{{ __('messages.recipient_number') }}</span>
                    <span class="value" dir="ltr">{{ $transaction->recipient_number }}</span>
                </div>
            @endif

            {{-- Category --}}
            <div class="receipt-row">
                <span class="label">{{ __('messages.category') }}</span>
                <span class="value">{{ $transaction->category->name }}</span>
            </div>

            {{-- Transaction Type --}}
            <div class="receipt-row">
                <span class="label">{{ __('messages.transaction_type') }}</span>
                <span class="value">
                    <span class="badge-type" style="background: {{ $transaction->type == 'deposit' ? '#28a745' : '#dc3545' }}; color: white;">
                        {{ __('messages.' . $transaction->type) }}
                    </span>
                </span>
            </div>
        </div>

        {{-- Amount Box --}}
        <div class="amount-box">
            <div class="type">{{ __('messages.amount') }}</div>
            <div class="amount" dir="ltr">
                {{ $transaction->type == 'deposit' ? '+' : '-' }}
                {{ number_format($transaction->amount, 2) }}
                {{ __('messages.currency') }}
            </div>
        </div>

        {{-- Amount in Words --}}
        <div class="amount-text">
            {{ __('messages.amount_in_words') }}: <strong>{{ $amountInWords ?? '' }}</strong>
        </div>

        {{-- Description --}}
        @if($transaction->description)
            <div class="receipt-row">
                <span class="label">{{ __('messages.description') }}</span>
                <span class="value">{{ $transaction->description }}</span>
            </div>
        @endif

        {{-- Footer --}}
        <div class="receipt-footer">
            <p>{{ __('messages.thank_you') }}</p>
            <p dir="ltr">{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
