<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\PurchaseInvoice;
use App\Models\Cashbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            return redirect()->intended('/home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function home()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $data = [];

        // Today's Statistics
        $data['todaySales'] = Sale::whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        $data['todaySalesCount'] = Sale::whereDate('sale_date', $today)
            ->where('status', 'completed')
            ->count();

        $data['todayReturns'] = SaleReturn::whereDate('return_date', $today)
            ->where('status', 'completed')
            ->sum('total_amount');

        // Monthly Statistics
        $data['monthlySales'] = Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->sum('total_amount');

        $data['monthlyPurchases'] = PurchaseInvoice::whereBetween('invoice_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'received')
            ->sum('total_amount');

        // Calculate monthly profit
        $monthlyCOGS = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startOfMonth, $endOfMonth])
            ->where('sales.status', 'completed')
            ->sum(DB::raw('sale_items.quantity * products.purchase_price'));

        $data['monthlyProfit'] = $data['monthlySales'] - $monthlyCOGS;

        // General Counts
        $data['totalProducts'] = Product::count();
        $data['lowStockProducts'] = Product::where('type', 'simple')
            ->where('quantity', '<=', 5)
            ->where('quantity', '>', 0)
            ->count();
        $data['outOfStockProducts'] = Product::where('type', 'simple')
            ->where('quantity', '<=', 0)
            ->count();

        $data['totalCustomers'] = Customer::where('is_default', false)->count();
        $data['totalSuppliers'] = Supplier::count();

        // Cashbox Balance
        $data['totalCashboxBalance'] = Cashbox::sum('current_balance');

        // Pending amounts
        $data['pendingFromCustomers'] = Customer::where('balance', '>', 0)->sum('balance');
        $data['pendingToSuppliers'] = Supplier::where('balance', '<', 0)->sum('balance') * -1;

        // Recent Sales (last 5)
        $data['recentSales'] = Sale::with(['customer', 'user'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top Selling Products Today
        $data['topProductsToday'] = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereDate('sales.sale_date', $today)
            ->where('sales.status', 'completed')
            ->select('products.name', 'products.code', DB::raw('SUM(sale_items.quantity) as total_qty'))
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Weekly Sales Chart Data
        $data['weeklySales'] = $this->getWeeklySalesData();

        // Admin only data
        if ($user->isAdmin()) {
            $data['totalUsers'] = User::count();
            $data['activeUsers'] = User::where('is_active', true)->count();
        }

        return view('home', $data);
    }

    private function getWeeklySalesData()
    {
        $labels = [];
        $totals = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');

            $total = Sale::whereDate('sale_date', $date)
                ->where('status', 'completed')
                ->sum('total_amount');

            $totals[] = (float) $total;
        }

        return [
            'labels' => $labels,
            'totals' => $totals,
        ];
    }
}
