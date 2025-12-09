<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Cashbox;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $customers = $query->latest()->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'balance' => ['nullable', 'numeric'],
        ]);

        $validated['balance'] = $validated['balance'] ?? 0;
        $validated['is_active'] = true;

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', __('messages.customer_created'));
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        // Prevent editing default customer
        if ($customer->is_default) {
            return redirect()->route('customers.index')
                ->with('error', __('messages.cannot_edit_default_customer'));
        }

        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        // Prevent updating default customer
        if ($customer->is_default) {
            return redirect()->route('customers.index')
                ->with('error', __('messages.cannot_edit_default_customer'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', __('messages.customer_updated'));
    }

    public function destroy(Customer $customer)
    {
        // Prevent deleting default customer
        if ($customer->is_default) {
            return redirect()->route('customers.index')
                ->with('error', __('messages.cannot_delete_default_customer'));
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', __('messages.customer_deleted'));
    }

    public function toggleStatus(Customer $customer)
    {
        // Prevent toggling default customer
        if ($customer->is_default) {
            return redirect()->route('customers.index')
                ->with('error', __('messages.cannot_modify_default_customer'));
        }

        $customer->update(['is_active' => !$customer->is_active]);

        return redirect()->route('customers.index')
            ->with('success', __('messages.customer_status_updated'));
    }

    /**
     * Display customer transactions
     */
    public function transactions(Request $request, Customer $customer)
    {
        $query = $customer->transactions()->with(['cashbox', 'category'])->latest();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->paginate(15);

        return view('customers.transactions', compact('customer', 'transactions'));
    }

    /**
     * Show form to add transaction for customer
     */
    public function addTransaction(Customer $customer)
    {
        // Prevent adding transactions for default customer
        if ($customer->is_default) {
            return redirect()->route('customers.show', $customer)
                ->with('error', __('messages.cannot_add_transaction_default_customer'));
        }

        $cashboxes = Cashbox::all();
        $categories = TransactionCategory::userCategories()->get();

        return view('customers.add-transaction', compact('customer', 'cashboxes', 'categories'));
    }

    /**
     * Store a new transaction for customer
     */
    public function storeTransaction(Request $request, Customer $customer)
    {
        // Prevent adding transactions for default customer
        if ($customer->is_default) {
            return redirect()->route('customers.show', $customer)
                ->with('error', __('messages.cannot_add_transaction_default_customer'));
        }

        $validated = $request->validate([
            'cashbox_id' => ['required', 'exists:cashboxes,id'],
            'transaction_category_id' => ['nullable', 'exists:transaction_categories,id'],
            'type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = Transaction::create([
                'cashbox_id' => $validated['cashbox_id'],
                'customer_id' => $customer->id,
                'transaction_category_id' => $validated['transaction_category_id'],
                'recipient_name' => $customer->name,
                'recipient_id' => $customer->phone,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'description' => $validated['description'] ?? null,
            ]);

            // Update cashbox balance
            $cashbox = Cashbox::find($validated['cashbox_id']);
            if ($validated['type'] === 'deposit') {
                // Customer paying us - money comes in
                $cashbox->current_balance += $validated['amount'];
            } else {
                // Giving customer refund/credit - money goes out
                $cashbox->current_balance -= $validated['amount'];
            }
            $cashbox->save();

            // Recalculate customer balance
            $customer->recalculateBalance();

            DB::commit();

            return redirect()->route('customers.transactions', $customer)
                ->with('success', __('messages.transaction_created'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.error_occurred'));
        }
    }

    /**
     * Display customer account statement
     */
    public function accountStatement(Request $request, Customer $customer)
    {
        $query = $customer->transactions()->with(['cashbox', 'category']);

        // Filter by date range
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $query->whereDate('created_at', '>=', $fromDate)
              ->whereDate('created_at', '<=', $toDate);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'asc')->get();

        // Calculate totals
        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');

        // Calculate opening balance (balance before from_date)
        // For customers: withdrawals increase debt, deposits decrease debt
        $openingBalance = $customer->transactions()
            ->whereDate('created_at', '<', $fromDate)
            ->selectRaw('COALESCE(SUM(CASE WHEN type = "withdrawal" THEN amount ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN type = "deposit" THEN amount ELSE 0 END), 0) as balance')
            ->value('balance') ?? 0;

        return view('customers.statement', compact(
            'customer',
            'transactions',
            'fromDate',
            'toDate',
            'totalDeposits',
            'totalWithdrawals',
            'openingBalance'
        ));
    }

    /**
     * Print customer account statement
     */
    public function printStatement(Request $request, Customer $customer)
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $query = $customer->transactions()->with(['cashbox', 'category'])
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'asc')->get();

        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');

        $openingBalance = $customer->transactions()
            ->whereDate('created_at', '<', $fromDate)
            ->selectRaw('COALESCE(SUM(CASE WHEN type = "withdrawal" THEN amount ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN type = "deposit" THEN amount ELSE 0 END), 0) as balance')
            ->value('balance') ?? 0;

        return view('customers.print-statement', compact(
            'customer',
            'transactions',
            'fromDate',
            'toDate',
            'totalDeposits',
            'totalWithdrawals',
            'openingBalance'
        ));
    }
}
