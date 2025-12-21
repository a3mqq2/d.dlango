<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.account_statement') }}</title>

    @if(app()->getLocale() == 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    @endif

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" }};
            background: white;
            padding: 20px;
            font-size: 12px;
            color: #333;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #b65f7a;
        }

        .logo {
            max-height: 80px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #b65f7a;
            margin-bottom: 10px;
        }

        .document-title {
            font-size: 18px;
            font-weight: 600;
            color: #666;
            margin-bottom: 20px;
        }

        /* Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: 600;
            padding: 5px 10px;
            width: 30%;
        }

        .info-value {
            display: table-cell;
            padding: 5px 10px;
        }

        /* Summary Cards */
        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .summary-card {
            flex: 1;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        .summary-card.opening {
            background: #f8f9fa;
        }

        .summary-card.deposits {
            background: #d4edda;
        }

        .summary-card.withdrawals {
            background: #f8d7da;
        }

        .summary-card.closing {
            background: #d1ecf1;
        }

        .summary-label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
            display: block;
        }

        .summary-amount {
            font-size: 18px;
            font-weight: 700;
        }

        .summary-card.opening .summary-amount {
            color: #b65f7a;
        }

        .summary-card.deposits .summary-amount {
            color: #28a745;
        }

        .summary-card.withdrawals .summary-amount {
            color: #dc3545;
        }

        .summary-card.closing .summary-amount {
            color: #17a2b8;
        }

        /* Table */
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .transactions-table thead {
            background: #b65f7a;
            color: white;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 10px 8px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            border: 1px solid #dee2e6;
        }

        .transactions-table th {
            font-weight: 600;
            font-size: 11px;
        }

        .transactions-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .transactions-table tbody tr:hover {
            background: #e9ecef;
        }

        .text-end {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }} !important;
        }

        .text-success {
            color: #28a745;
            font-weight: 600;
        }

        .text-danger {
            color: #dc3545;
            font-weight: 600;
        }

        .text-primary {
            color: #b65f7a;
            font-weight: 600;
        }

        .text-muted {
            color: #6c757d;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 10px;
            border-radius: 3px;
            background: #e9ecef;
            color: #333;
            border: 1px solid #dee2e6;
        }

        /* Footer */
        .transactions-table tfoot {
            background: #f8f9fa;
            font-weight: 700;
        }

        .transactions-table tfoot td {
            padding: 12px 8px;
            font-size: 12px;
        }

        .final-balance {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 700;
        }

        .final-balance.positive {
            background: #28a745;
            color: white;
        }

        .final-balance.negative {
            background: #dc3545;
            color: white;
        }

        .final-balance.neutral {
            background: #6c757d;
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #999;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
            }

            @page {
                size: A4 portrait;
                margin: 1cm;
            }

            .transactions-table thead {
                background: #b65f7a !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .summary-card {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .text-success,
            .text-danger,
            .text-primary {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .final-balance {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .transactions-table tbody tr:nth-child(even) {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            @if(file_exists(public_path('logo.png')))
                <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">
            @endif
            <div class="document-title">{{ __('messages.account_statement') }}</div>
        </div>

        {{-- Info Section --}}
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">{{ __('messages.cashbox') }}:</div>
                <div class="info-value">{{ $selectedCashbox->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ __('messages.period') }}:</div>
                <div class="info-value" dir="ltr">
                    {{ request('from_date') }} {{ __('messages.to') }} {{ request('to_date') }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">{{ __('messages.print_date') }}:</div>
                <div class="info-value" dir="ltr">{{ now()->format('Y-m-d H:i') }}</div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="summary-cards">
            <div class="summary-card opening">
                <span class="summary-label">{{ __('messages.opening_balance') }}</span>
                <div class="summary-amount" dir="ltr">
                    {{ number_format($openingBalance, 2) }} {{ __('messages.currency') }}
                </div>
            </div>
            <div class="summary-card deposits">
                <span class="summary-label">{{ __('messages.total_deposits') }}</span>
                <div class="summary-amount" dir="ltr">
                    +{{ number_format($totalDeposits, 2) }} {{ __('messages.currency') }}
                </div>
            </div>
            <div class="summary-card withdrawals">
                <span class="summary-label">{{ __('messages.total_withdrawals') }}</span>
                <div class="summary-amount" dir="ltr">
                    -{{ number_format($totalWithdrawals, 2) }} {{ __('messages.currency') }}
                </div>
            </div>
            <div class="summary-card closing">
                <span class="summary-label">{{ __('messages.closing_balance') }}</span>
                <div class="summary-amount" dir="ltr">
                    {{ number_format($closingBalance, 2) }} {{ __('messages.currency') }}
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        @if($transactions->count() > 0)
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th width="12%">{{ __('messages.date') }}</th>
                        <th width="30%">{{ __('messages.description') }}</th>
                        <th width="18%" class="text-end">{{ __('messages.deposits') }}</th>
                        <th width="18%" class="text-end">{{ __('messages.withdrawals') }}</th>
                        <th width="22%" class="text-end">{{ __('messages.balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningBalance = $openingBalance; @endphp
                    @foreach($transactions as $transaction)
                        @php
                            if ($transaction->type == 'deposit') {
                                $runningBalance += $transaction->amount;
                            } else {
                                $runningBalance -= $transaction->amount;
                            }
                        @endphp
                        <tr>
                            <td>
                                <small dir="ltr">{{ $transaction->created_at->format('Y-m-d') }}</small>
                            </td>
                            <td>
                                <strong>{{ $transaction->recipient_name }}</strong>
                                @if($transaction->description)
                                    <br><small class="text-muted">{{ $transaction->description }}</small>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($transaction->type == 'deposit')
                                    <span class="text-success" dir="ltr">
                                        {{ number_format($transaction->amount, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($transaction->type == 'withdrawal')
                                    <span class="text-danger" dir="ltr">
                                        {{ number_format($transaction->amount, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="text-{{ $runningBalance > 0 ? 'success' : ($runningBalance < 0 ? 'danger' : 'muted') }}" dir="ltr">
                                    {{ number_format($runningBalance, 2) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end">{{ __('messages.totals') }}</td>
                        <td class="text-end text-success" dir="ltr">
                            {{ number_format($totalDeposits, 2) }}
                        </td>
                        <td class="text-end text-danger" dir="ltr">
                            {{ number_format($totalWithdrawals, 2) }}
                        </td>
                        <td class="text-end">
                            <span class="final-balance {{ $closingBalance > 0 ? 'positive' : ($closingBalance < 0 ? 'negative' : 'neutral') }}" dir="ltr">
                                {{ number_format($closingBalance, 2) }} {{ __('messages.currency') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end">{{ __('messages.net_change') }}</td>
                        <td colspan="3" class="text-end">
                            <span class="text-{{ ($closingBalance - $openingBalance) > 0 ? 'success' : (($closingBalance - $openingBalance) < 0 ? 'danger' : 'muted') }}" dir="ltr">
                                {{ ($closingBalance - $openingBalance) > 0 ? '+' : '' }}
                                {{ number_format($closingBalance - $openingBalance, 2) }} {{ __('messages.currency') }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="empty-state">
                <div>&#128196;</div>
                <h5>{{ __('messages.no_transactions_in_period') }}</h5>
            </div>
        @endif
    </div>

    <script>
        // Auto print on load
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
