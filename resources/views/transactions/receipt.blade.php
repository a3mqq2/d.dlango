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
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 10mm;
        }

        .receipt-container {
            width: 210mm;
            height: 148mm;
            margin: 0 auto;
            background: white;
            padding: 15mm;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        /* Decorative corner elements */
        .receipt-container::before,
        .receipt-container::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            border: 3px solid #b65f7a;
        }

        .receipt-container::before {
            top: 10mm;
            {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 10mm;
            border-bottom: none;
            border-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: none;
        }

        .receipt-container::after {
            bottom: 10mm;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 10mm;
            border-top: none;
            border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: none;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #b65f7a;
        }

        .logo-section {
            flex: 0 0 auto;
        }

        .logo {
            max-width: 120px;
            max-height: 70px;
            object-fit: contain;
        }

        .company-info {
            margin-top: 8px;
        }

        .company-name {
            font-size: 20px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .company-tagline {
            font-size: 9px;
            color: #6c757d;
            font-style: italic;
        }

        .receipt-info {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
            flex: 0 0 auto;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: 900;
            color: #b65f7a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .receipt-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
        }

        .receipt-date {
            color: #6c757d;
            font-size: 11px;
            font-weight: 600;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 15px;
            border-radius: 10px;
            border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 4px solid #b65f7a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .info-card h3 {
            font-size: 12px;
            color: #b65f7a;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #b65f7a;
            padding-bottom: 6px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e9ecef;
            font-size: 11px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6c757d;
            font-weight: 600;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 700;
        }

        .category-badge {
            display: inline-block;
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .amount-showcase {
            background: {{ $transaction->type == 'deposit'
                ? 'linear-gradient(135deg, #00b09b 0%, #96c93d 100%)'
                : 'linear-gradient(135deg, #eb3349 0%, #f45c43 100%)' }};
            color: white;
            text-align: center;
            padding: 25px;
            border-radius: 15px;
            margin: 25px 0;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }

        .amount-showcase::before {
            content: '';
            position: absolute;
            top: -50%;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .amount-label {
            font-size: 13px;
            opacity: 0.95;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .amount-value {
            font-size: 42px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .currency-label {
            font-size: 12px;
            opacity: 0.9;
            font-weight: 600;
        }

        .transaction-badge {
            display: inline-block;
            background: {{ $transaction->type == 'deposit' ? 'rgba(255,255,255,0.3)' : 'rgba(255,255,255,0.3)' }};
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 10px;
            border: 2px solid rgba(255,255,255,0.5);
        }

        .description-section {
            background: #fff9e6;
            border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 4px solid #ffc107;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
        }

        .description-section strong {
            color: #856404;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .description-section p {
            color: #856404;
            font-size: 11px;
            margin: 5px 0 0 0;
            line-height: 1.5;
        }

        .footer-section {
            position: absolute;
            bottom: 12mm;
            left: 15mm;
            right: 15mm;
            text-align: center;
            padding-top: 12px;
            border-top: 3px solid #b65f7a;
        }

        .footer-message {
            color: #b65f7a;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .footer-date {
            color: #adb5bd;
            font-size: 10px;
            font-weight: 600;
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 30px;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 25px;
            font-weight: 600;
        }

        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(182, 95, 122, 0.03);
            font-weight: 900;
            pointer-events: none;
            z-index: 0;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <button class="btn btn-primary btn-lg print-button no-print" onclick="window.print()">
        <i class="ti ti-printer me-2"></i>{{ __('messages.print') }}
    </button>

    <div class="receipt-container">
        {{-- Watermark --}}
        <div class="watermark">PAID</div>

        {{-- Header Section --}}
        <div class="header-section">
            <div class="logo-section">
                <img src="{{ asset('logo.png') }}" class="logo" alt="Logo">
                <div class="company-info">
                    <div class="company-name">{{ config('app.name', 'Dr.dlango') }}</div>
                    <div class="company-tagline">{{ __('messages.payment_receipt') }}</div>
                </div>
            </div>
            <div class="receipt-info">
                <div class="receipt-title">{{ __('messages.receipt') }}</div>
                <div class="receipt-number" dir="ltr">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div class="receipt-date" dir="ltr">
                    <i class="ti ti-calendar"></i> {{ $transaction->created_at->format('Y-m-d H:i A') }}
                </div>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="content-grid">
            {{-- Transaction Details --}}
            <div class="info-card">
                <h3><i class="ti ti-file-text me-1"></i>{{ __('messages.transaction_details') }}</h3>
                <div class="info-row">
                    <span class="info-label">{{ __('messages.recipient_payer_name') }}</span>
                    <span class="info-value">{{ $transaction->recipient_name }}</span>
                </div>
                @if($transaction->recipient_id)
                <div class="info-row">
                    <span class="info-label">{{ __('messages.phone') }}</span>
                    <span class="info-value" dir="ltr">{{ $transaction->recipient_id }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">{{ __('messages.category') }}</span>
                    <span class="info-value">
                        <span class="category-badge">{{ $transaction->category->name }}</span>
                    </span>
                </div>
            </div>

            {{-- Payment Details --}}
            <div class="info-card">
                <h3><i class="ti ti-credit-card me-1"></i>{{ __('messages.payment_details') }}</h3>
                <div class="info-row">
                    <span class="info-label">{{ __('messages.payment_type') }}</span>
                    <span class="info-value">
                        @if($transaction->cashbox)
                            <i class="ti ti-cash"></i> {{ $transaction->cashbox->name }}
                        @else
                            <i class="ti ti-calendar-due"></i> {{ __('messages.credit_transaction') }}
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('messages.transaction_type') }}</span>
                    <span class="info-value">
                        {{ $transaction->type == 'deposit' ? __('messages.deposit') : __('messages.withdrawal') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('messages.date') }}</span>
                    <span class="info-value" dir="ltr">{{ $transaction->created_at->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        {{-- Amount Showcase --}}
        <div class="amount-showcase">
            <div class="amount-label">{{ __('messages.amount') }}</div>
            <div class="amount-value" dir="ltr">
                {{ $transaction->type == 'deposit' ? '+' : '-' }}
                {{ number_format($transaction->amount, 2) }}
            </div>
            <div class="currency-label">{{ __('messages.currency') }}</div>
            <div class="transaction-badge">
                {{ $transaction->type == 'deposit' ? __('messages.deposit') : __('messages.withdrawal') }}
            </div>
        </div>

        {{-- Description --}}
        @if($transaction->description)
        <div class="description-section">
            <strong><i class="ti ti-notes"></i> {{ __('messages.description') }}:</strong>
            <p>{{ $transaction->description }}</p>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer-section">
            <div class="footer-message">{{ __('messages.thank_you') }}</div>
            <div class="footer-date" dir="ltr">
                {{ __('messages.printed_on') }}: {{ now()->format('Y-m-d H:i:s') }}
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
