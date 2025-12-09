<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.supplier_statement') }} - {{ $supplier->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .info-box {
            text-align: center;
        }
        .info-box label {
            display: block;
            font-size: 10px;
            color: #666;
            margin-bottom: 3px;
        }
        .info-box span {
            font-size: 14px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-end { text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}; }
        .fw-bold { font-weight: bold; }
        .opening-row {
            background: #e9ecef;
        }
        .totals-row {
            background: #d4edda;
            font-weight: bold;
        }
        .summary-section {
            display: flex;
            justify-content: space-around;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-box {
            text-align: center;
            padding: 10px 20px;
        }
        .summary-box label {
            display: block;
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-box span {
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>{{ __('messages.app_name') }}</h1>
            <h2>{{ __('messages.supplier_statement') }}</h2>
            <p>{{ $supplier->name }} - {{ $supplier->phone }}</p>
            <p style="margin-top: 10px; font-size: 11px;">
                {{ __('messages.from') }} {{ $fromDate }} {{ __('messages.to') }} {{ $toDate }}
            </p>
        </div>

        {{-- Info Section --}}
        <div class="info-section">
            <div class="info-box">
                <label>{{ __('messages.opening_balance') }}</label>
                <span class="{{ $openingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($openingBalance, 2) }} {{ __('messages.currency') }}
                </span>
            </div>
            <div class="info-box">
                <label>{{ __('messages.total_deposits') }}</label>
                <span class="text-success">+{{ number_format($totalDeposits, 2) }} {{ __('messages.currency') }}</span>
            </div>
            <div class="info-box">
                <label>{{ __('messages.total_withdrawals') }}</label>
                <span class="text-danger">-{{ number_format($totalWithdrawals, 2) }} {{ __('messages.currency') }}</span>
            </div>
            <div class="info-box">
                <label>{{ __('messages.closing_balance') }}</label>
                @php $closingBalance = $openingBalance + $totalDeposits - $totalWithdrawals; @endphp
                <span class="{{ $closingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($closingBalance, 2) }} {{ __('messages.currency') }}
                </span>
            </div>
        </div>

        {{-- Statement Table --}}
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">{{ __('messages.date') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th width="12%">{{ __('messages.deposit') }}</th>
                    <th width="12%">{{ __('messages.withdrawal') }}</th>
                    <th width="15%">{{ __('messages.balance') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- Opening Balance Row --}}
                <tr class="opening-row">
                    <td colspan="5" class="fw-bold">{{ __('messages.opening_balance') }}</td>
                    <td class="fw-bold {{ $openingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($openingBalance, 2) }}
                    </td>
                </tr>

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
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            {{ $transaction->description ?? '-' }}
                            @if($transaction->category)
                                <br><small style="color: #666;">{{ $transaction->category->name }}</small>
                            @endif
                        </td>
                        <td class="text-success">
                            {{ $transaction->type == 'deposit' ? number_format($transaction->amount, 2) : '-' }}
                        </td>
                        <td class="text-danger">
                            {{ $transaction->type == 'withdrawal' ? number_format($transaction->amount, 2) : '-' }}
                        </td>
                        <td class="fw-bold {{ $runningBalance >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($runningBalance, 2) }}
                        </td>
                    </tr>
                @endforeach

                {{-- Totals Row --}}
                <tr class="totals-row">
                    <td colspan="3" class="text-end">{{ __('messages.totals') }}</td>
                    <td class="text-success">{{ number_format($totalDeposits, 2) }}</td>
                    <td class="text-danger">{{ number_format($totalWithdrawals, 2) }}</td>
                    <td class="{{ $closingBalance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($closingBalance, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Footer --}}
        <div class="footer">
            <p>{{ __('messages.printed_on') }}: {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>{{ __('messages.app_name') }} - {{ __('messages.all_rights_reserved') }}</p>
        </div>

        {{-- Print Button --}}
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer;">
                {{ __('messages.print') }}
            </button>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
