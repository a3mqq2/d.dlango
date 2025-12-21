<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\PurchaseInvoice;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index(Request $request)
    {
        // Default to current month
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Sales Statistics
        $salesStats = $this->getSalesStats($start, $end);

        // Purchases Statistics
        $purchasesStats = $this->getPurchasesStats($start, $end);

        // Returns Statistics
        $returnsStats = $this->getReturnsStats($start, $end);

        // Expenses (Withdrawals)
        $expensesStats = $this->getExpensesStats($start, $end);

        // Calculate Profits
        $profitStats = $this->calculateProfits($salesStats, $purchasesStats, $returnsStats, $expensesStats);

        // Top Selling Products
        $topProducts = $this->getTopSellingProducts($start, $end);

        // Top Customers
        $topCustomers = $this->getTopCustomers($start, $end);

        // Sales by Payment Method
        $salesByPayment = $this->getSalesByPaymentMethod($start, $end);

        // Daily Sales Chart Data
        $dailySales = $this->getDailySalesData($start, $end);

        // Sales by User
        $salesByUser = $this->getSalesByUser($start, $end);

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'salesStats',
            'purchasesStats',
            'returnsStats',
            'expensesStats',
            'profitStats',
            'topProducts',
            'topCustomers',
            'salesByPayment',
            'dailySales',
            'salesByUser'
        ));
    }

    /**
     * Get sales statistics
     */
    private function getSalesStats($start, $end)
    {
        $sales = Sale::whereBetween('sale_date', [$start, $end])
            ->where('status', 'completed');

        return [
            'total_sales' => $sales->count(),
            'total_revenue' => (float) $sales->sum('total_amount'),
            'total_discount' => (float) $sales->sum('discount') + (float) $sales->sum('coupon_discount'),
            'total_paid' => (float) $sales->sum('paid_amount'),
            'total_remaining' => (float) $sales->sum('remaining_amount'),
            'average_sale' => $sales->count() > 0 ? (float) $sales->sum('total_amount') / $sales->count() : 0,
            'items_sold' => SaleItem::whereHas('sale', function ($q) use ($start, $end) {
                $q->whereBetween('sale_date', [$start, $end])->where('status', 'completed');
            })->sum('quantity'),
        ];
    }

    /**
     * Get purchases statistics
     */
    private function getPurchasesStats($start, $end)
    {
        $purchases = PurchaseInvoice::whereBetween('invoice_date', [$start, $end])
            ->where('status', 'received');

        return [
            'total_purchases' => $purchases->count(),
            'total_cost' => (float) $purchases->sum('total_amount'),
            'expected_profit' => (float) $purchases->sum('total_profit'),
        ];
    }

    /**
     * Get returns statistics
     */
    private function getReturnsStats($start, $end)
    {
        $returns = SaleReturn::whereBetween('return_date', [$start, $end])
            ->where('status', 'completed');

        return [
            'total_returns' => $returns->count(),
            'total_refunded' => (float) $returns->sum('total_amount'),
        ];
    }

    /**
     * Get expenses statistics
     */
    private function getExpensesStats($start, $end)
    {
        $expenses = Transaction::whereBetween('created_at', [$start, $end])
            ->where('type', 'withdrawal')
            ->whereNull('supplier_id')
            ->whereNull('customer_id');

        $deposits = Transaction::whereBetween('created_at', [$start, $end])
            ->where('type', 'deposit')
            ->whereNull('supplier_id')
            ->whereNull('customer_id');

        return [
            'total_expenses' => (float) $expenses->sum('amount'),
            'total_other_income' => (float) $deposits->sum('amount'),
            'expenses_list' => Transaction::whereBetween('created_at', [$start, $end])
                ->where('type', 'withdrawal')
                ->whereNull('supplier_id')
                ->whereNull('customer_id')
                ->select('recipient_name', 'description', DB::raw('SUM(amount) as total'))
                ->groupBy('recipient_name', 'description')
                ->get(),
        ];
    }

    /**
     * Calculate profit statistics
     */
    private function calculateProfits($salesStats, $purchasesStats, $returnsStats, $expensesStats)
    {
        // Gross Profit = Revenue - Cost of Goods Sold (COGS)
        // For simplicity, we'll use expected profit from purchases as a proxy
        // In reality, you'd calculate based on actual items sold and their purchase prices

        // Get actual cost of goods sold
        $cogs = $this->getCostOfGoodsSold($salesStats);

        $grossRevenue = $salesStats['total_revenue'];
        $netRevenue = $grossRevenue - $returnsStats['total_refunded'];
        $grossProfit = $netRevenue - $cogs;
        $netProfit = $grossProfit - $expensesStats['total_expenses'] + $expensesStats['total_other_income'];

        $grossProfitMargin = $netRevenue > 0 ? ($grossProfit / $netRevenue) * 100 : 0;
        $netProfitMargin = $netRevenue > 0 ? ($netProfit / $netRevenue) * 100 : 0;

        return [
            'gross_revenue' => $grossRevenue,
            'net_revenue' => $netRevenue,
            'cost_of_goods_sold' => $cogs,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
            'gross_profit_margin' => round($grossProfitMargin, 2),
            'net_profit_margin' => round($netProfitMargin, 2),
        ];
    }

    /**
     * Calculate cost of goods sold based on sale items
     */
    private function getCostOfGoodsSold($salesStats)
    {
        // This calculates the actual purchase price of items sold
        return SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status', 'completed')
            ->sum(DB::raw('sale_items.quantity * products.purchase_price'));
    }

    /**
     * Get top selling products
     */
    private function getTopSellingProducts($start, $end, $limit = 10)
    {
        return SaleItem::select(
                'products.id',
                'products.name',
                'products.code',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.subtotal) as total_revenue')
            )
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->where('sales.status', 'completed')
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top customers by purchases
     */
    private function getTopCustomers($start, $end, $limit = 10)
    {
        return Customer::select(
                'customers.id',
                'customers.name',
                'customers.phone',
                DB::raw('COUNT(sales.id) as total_orders'),
                DB::raw('SUM(sales.total_amount) as total_spent')
            )
            ->join('sales', 'customers.id', '=', 'sales.customer_id')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->where('sales.status', 'completed')
            ->groupBy('customers.id', 'customers.name', 'customers.phone')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();
    }

    /**
     * Get sales by payment method
     */
    private function getSalesByPaymentMethod($start, $end)
    {
        return Sale::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();
    }

    /**
     * Get daily sales data for chart
     */
    private function getDailySalesData($start, $end)
    {
        $sales = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->whereBetween('sale_date', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $totals = [];
        $counts = [];

        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('m/d');

            $dayData = $sales->firstWhere('date', $dateStr);
            $totals[] = $dayData ? (float) $dayData->total : 0;
            $counts[] = $dayData ? $dayData->count : 0;

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
            'counts' => $counts,
        ];
    }

    /**
     * Get sales by user
     */
    private function getSalesByUser($start, $end)
    {
        return Sale::select(
                'users.id',
                'users.name',
                DB::raw('COUNT(sales.id) as total_sales'),
                DB::raw('SUM(sales.total_amount) as total_revenue')
            )
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->whereBetween('sales.sale_date', [$start, $end])
            ->where('sales.status', 'completed')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    /**
     * Export report as PDF
     */
    public function export(Request $request)
    {
        // TODO: Implement PDF export
        return back()->with('info', __('messages.coming_soon'));
    }

    /**
     * Print report
     */
    public function print(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $salesStats = $this->getSalesStats($start, $end);
        $purchasesStats = $this->getPurchasesStats($start, $end);
        $returnsStats = $this->getReturnsStats($start, $end);
        $expensesStats = $this->getExpensesStats($start, $end);
        $profitStats = $this->calculateProfits($salesStats, $purchasesStats, $returnsStats, $expensesStats);
        $topProducts = $this->getTopSellingProducts($start, $end);
        $salesByUser = $this->getSalesByUser($start, $end);

        return view('reports.print', compact(
            'startDate',
            'endDate',
            'salesStats',
            'purchasesStats',
            'returnsStats',
            'expensesStats',
            'profitStats',
            'topProducts',
            'salesByUser'
        ));
    }
}
