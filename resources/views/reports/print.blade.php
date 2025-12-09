<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.reports') }} - {{ $startDate }} {{ __('messages.to') }} {{ $endDate }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Changa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Changa', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #b65f7a;
        }
        .header h1 {
            font-size: 24px;
            color: #b65f7a;
            margin-bottom: 5px;
        }
        .header .period {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #b65f7a;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-box .value {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .stat-box .value.positive {
            color: #28a745;
        }
        .stat-box .value.negative {
            color: #dc3545;
        }
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            padding: 8px 10px;
            text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
            border-bottom: 1px solid #eee;
        }
        table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        table tbody tr:hover {
            background: #f8f9fa;
        }
        .text-end {
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }} !important;
        }
        .text-center {
            text-align: center !important;
        }
        .text-success {
            color: #28a745;
        }
        .text-danger {
            color: #dc3545;
        }
        .summary-table td {
            padding: 6px 10px;
        }
        .summary-table .label-col {
            color: #666;
            width: 60%;
        }
        .summary-table .value-col {
            font-weight: 600;
            text-align: {{ app()->getLocale() == 'ar' ? 'left' : 'right' }};
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #b65f7a; color: white; border: none; border-radius: 5px; cursor: pointer;">
            {{ __('messages.print') }}
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            {{ __('messages.close') }}
        </button>
    </div>

    <div class="header">
        <h1>{{ __('messages.financial_report') }}</h1>
        <p class="period">
            {{ __('messages.from') }}: {{ \Carbon\Carbon::parse($startDate)->format('Y/m/d') }}
            -
            {{ __('messages.to') }}: {{ \Carbon\Carbon::parse($endDate)->format('Y/m/d') }}
        </p>
        <p style="font-size: 11px; color: #999; margin-top: 5px;">
            {{ __('messages.printed_at') }}: {{ now()->format('Y/m/d H:i') }}
        </p>
    </div>

    {{-- Main Statistics --}}
    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">{{ __('messages.total_sales') }}</div>
            <div class="value">{{ number_format($salesStats['total_revenue'], 2) }}</div>
            <div style="font-size: 10px; color: #666;">{{ $salesStats['total_sales'] }} {{ __('messages.invoice') }}</div>
        </div>
        <div class="stat-box">
            <div class="label">{{ __('messages.total_purchases') }}</div>
            <div class="value">{{ number_format($purchasesStats['total_cost'], 2) }}</div>
            <div style="font-size: 10px; color: #666;">{{ $purchasesStats['total_purchases'] }} {{ __('messages.invoice') }}</div>
        </div>
        <div class="stat-box">
            <div class="label">{{ __('messages.gross_profit') }}</div>
            <div class="value {{ $profitStats['gross_profit'] >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($profitStats['gross_profit'], 2) }}
            </div>
            <div style="font-size: 10px; color: #666;">{{ $profitStats['gross_profit_margin'] }}%</div>
        </div>
        <div class="stat-box">
            <div class="label">{{ __('messages.net_profit') }}</div>
            <div class="value {{ $profitStats['net_profit'] >= 0 ? 'positive' : 'negative' }}">
                {{ number_format($profitStats['net_profit'], 2) }}
            </div>
            <div style="font-size: 10px; color: #666;">{{ $profitStats['net_profit_margin'] }}%</div>
        </div>
    </div>

    <div class="two-columns">
        {{-- Sales Details --}}
        <div class="section">
            <h3 class="section-title">{{ __('messages.sales_details') }}</h3>
            <table class="summary-table">
                <tr>
                    <td class="label-col">{{ __('messages.total_revenue') }}</td>
                    <td class="value-col">{{ number_format($salesStats['total_revenue'], 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.total_discounts') }}</td>
                    <td class="value-col text-danger">-{{ number_format($salesStats['total_discount'], 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.returns') }}</td>
                    <td class="value-col text-danger">-{{ number_format($returnsStats['total_refunded'], 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #ddd;">
                    <td class="label-col" style="font-weight: 600;">{{ __('messages.net_sales') }}</td>
                    <td class="value-col" style="font-weight: 600;">{{ number_format($profitStats['net_revenue'], 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.items_sold') }}</td>
                    <td class="value-col">{{ number_format($salesStats['items_sold']) }}</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.average_sale') }}</td>
                    <td class="value-col">{{ number_format($salesStats['average_sale'], 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.paid_amount') }}</td>
                    <td class="value-col text-success">{{ number_format($salesStats['total_paid'], 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.remaining_amount') }}</td>
                    <td class="value-col" style="color: #ffc107;">{{ number_format($salesStats['total_remaining'], 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- Profit Details --}}
        <div class="section">
            <h3 class="section-title">{{ __('messages.profit_details') }}</h3>
            <table class="summary-table">
                <tr>
                    <td class="label-col">{{ __('messages.net_sales') }}</td>
                    <td class="value-col">{{ number_format($profitStats['net_revenue'], 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.cost_of_goods_sold') }}</td>
                    <td class="value-col text-danger">-{{ number_format($profitStats['cost_of_goods_sold'], 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #ddd;">
                    <td class="label-col" style="font-weight: 600;">{{ __('messages.gross_profit') }}</td>
                    <td class="value-col {{ $profitStats['gross_profit'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: 600;">
                        {{ number_format($profitStats['gross_profit'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.expenses') }}</td>
                    <td class="value-col text-danger">-{{ number_format($expensesStats['total_expenses'], 2) }}</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.other_income') }}</td>
                    <td class="value-col text-success">+{{ number_format($expensesStats['total_other_income'], 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #ddd;">
                    <td class="label-col" style="font-weight: 600;">{{ __('messages.net_profit') }}</td>
                    <td class="value-col {{ $profitStats['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}" style="font-weight: 600;">
                        {{ number_format($profitStats['net_profit'], 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.gross_profit_margin') }}</td>
                    <td class="value-col">{{ $profitStats['gross_profit_margin'] }}%</td>
                </tr>
                <tr>
                    <td class="label-col">{{ __('messages.net_profit_margin') }}</td>
                    <td class="value-col">{{ $profitStats['net_profit_margin'] }}%</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="two-columns">
        {{-- Top Products --}}
        <div class="section">
            <h3 class="section-title">{{ __('messages.top_selling_products') }}</h3>
            @if($topProducts->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.product') }}</th>
                            <th class="text-center">{{ __('messages.quantity') }}</th>
                            <th class="text-end">{{ __('messages.revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td class="text-center">{{ number_format($product->total_quantity) }}</td>
                                <td class="text-end">{{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #666;">{{ __('messages.no_data') }}</p>
            @endif
        </div>

        {{-- Sales by User --}}
        <div class="section">
            <h3 class="section-title">{{ __('messages.sales_by_user') }}</h3>
            @if($salesByUser->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('messages.user') }}</th>
                            <th class="text-center">{{ __('messages.sales_count') }}</th>
                            <th class="text-end">{{ __('messages.total_revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salesByUser as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td class="text-center">{{ $user->total_sales }}</td>
                                <td class="text-end">{{ number_format($user->total_revenue, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="text-align: center; color: #666;">{{ __('messages.no_data') }}</p>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>{{ __('messages.app_name') }} - {{ __('messages.all_rights_reserved') }}</p>
    </div>
</body>
</html>
