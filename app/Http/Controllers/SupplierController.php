<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\Cashbox;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Supplier::latest();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'balance' => ['nullable', 'numeric'],
        ]);

        $validated['balance'] = $validated['balance'] ?? 0;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', __('messages.supplier_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'balance' => ['nullable', 'numeric'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', __('messages.supplier_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', __('messages.supplier_deleted'));
    }

    /**
     * Display supplier transactions
     */
    public function transactions(Request $request, Supplier $supplier)
    {
        $query = $supplier->transactions()->with(['cashbox'])->latest();

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

        return view('suppliers.transactions', compact('supplier', 'transactions'));
    }

    /**
     * Show form to add transaction for supplier
     */
    public function addTransaction(Supplier $supplier)
    {
        $cashboxes = Cashbox::all();

        return view('suppliers.add-transaction', compact('supplier', 'cashboxes'));
    }

    /**
     * Store a new transaction for supplier
     */
    public function storeTransaction(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'cashbox_id' => ['required', 'exists:cashboxes,id'],
            'type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = Transaction::create([
                'cashbox_id' => $validated['cashbox_id'],
                'supplier_id' => $supplier->id,
                'recipient_name' => $supplier->name,
                'recipient_id' => $supplier->phone,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'description' => $validated['description'] ?? null,
            ]);

            // Update cashbox balance
            // For suppliers: deposit = paying supplier (withdraw from cashbox)
            //                withdrawal = credit purchase (increases our debt)
            $cashbox = Cashbox::find($validated['cashbox_id']);
            if ($validated['type'] === 'deposit') {
                // Paying supplier = withdraw from cashbox
                $cashbox->current_balance -= $validated['amount'];
            } else {
                // Withdrawal = credit purchase, increases our debt to supplier
                // No cash movement, so no cashbox effect
                // (The debt is tracked in supplier balance)
            }
            $cashbox->save();

            // Recalculate supplier balance
            $supplier->recalculateBalance();

            DB::commit();

            return redirect()->route('suppliers.transactions', $supplier)
                ->with('success', __('messages.transaction_created'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('messages.error_occurred'));
        }
    }

    /**
     * Display supplier account statement
     */
    public function accountStatement(Request $request, Supplier $supplier)
    {
        $query = $supplier->transactions()->with(['cashbox']);

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
        $openingBalance = $supplier->transactions()
            ->whereDate('created_at', '<', $fromDate)
            ->selectRaw('COALESCE(SUM(CASE WHEN type = "deposit" THEN amount ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN type = "withdrawal" THEN amount ELSE 0 END), 0) as balance')
            ->value('balance') ?? 0;

        return view('suppliers.statement', compact(
            'supplier',
            'transactions',
            'fromDate',
            'toDate',
            'totalDeposits',
            'totalWithdrawals',
            'openingBalance'
        ));
    }

    /**
     * Print supplier account statement
     */
    public function printStatement(Request $request, Supplier $supplier)
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $query = $supplier->transactions()->with(['cashbox'])
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->orderBy('created_at', 'asc')->get();

        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');

        $openingBalance = $supplier->transactions()
            ->whereDate('created_at', '<', $fromDate)
            ->selectRaw('COALESCE(SUM(CASE WHEN type = "deposit" THEN amount ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN type = "withdrawal" THEN amount ELSE 0 END), 0) as balance')
            ->value('balance') ?? 0;

        return view('suppliers.print-statement', compact(
            'supplier',
            'transactions',
            'fromDate',
            'toDate',
            'totalDeposits',
            'totalWithdrawals',
            'openingBalance'
        ));
    }
}
