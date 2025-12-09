<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Cashbox;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['cashbox', 'category']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('cashbox_id')) {
            $query->where('cashbox_id', $request->cashbox_id);
        }

        if ($request->filled('category_id')) {
            $query->where('transaction_category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest()->paginate(10);
        $cashboxes = Cashbox::all();
        $categories = TransactionCategory::all(); // All categories for filtering

        return view('transactions.index', compact('transactions', 'cashboxes', 'categories'));
    }

    public function create()
    {
        $cashboxes = Cashbox::all();
        $categories = TransactionCategory::userCategories()->get(); // Only user categories for selection

        return view('transactions.create', compact('cashboxes', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cashbox_id' => ['required', 'exists:cashboxes,id'],
            'transaction_category_id' => ['required', 'exists:transaction_categories,id'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_id' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ]);

        // إنشاء الحركة المالية
        $transaction = Transaction::create($validated);

        // تحديث رصيد الخزينة
        $cashbox = Cashbox::find($validated['cashbox_id']);
        if ($validated['type'] === 'deposit') {
            $cashbox->current_balance += $validated['amount'];
        } else {
            $cashbox->current_balance -= $validated['amount'];
        }
        $cashbox->save();

        return redirect()->route('transactions.index')
            ->with('success', __('messages.transaction_created'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['cashbox', 'category']);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $cashboxes = Cashbox::all();
        $categories = TransactionCategory::userCategories()->get(); // Only user categories for selection

        return view('transactions.edit', compact('transaction', 'cashboxes', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'cashbox_id' => ['required', 'exists:cashboxes,id'],
            'transaction_category_id' => ['required', 'exists:transaction_categories,id'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_id' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:deposit,withdrawal'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string'],
        ]);

        // إرجاع تأثير الحركة القديمة على الرصيد
        $oldCashbox = Cashbox::find($transaction->cashbox_id);
        if ($transaction->type === 'deposit') {
            $oldCashbox->current_balance -= $transaction->amount;
        } else {
            $oldCashbox->current_balance += $transaction->amount;
        }
        $oldCashbox->save();

        // تحديث الحركة
        $transaction->update($validated);

        // تطبيق تأثير الحركة الجديدة
        $newCashbox = Cashbox::find($validated['cashbox_id']);
        if ($validated['type'] === 'deposit') {
            $newCashbox->current_balance += $validated['amount'];
        } else {
            $newCashbox->current_balance -= $validated['amount'];
        }
        $newCashbox->save();

        return redirect()->route('transactions.index')
            ->with('success', __('messages.transaction_updated'));
    }

    public function destroy(Transaction $transaction)
    {
        // إرجاع تأثير الحركة على الرصيد
        $cashbox = Cashbox::find($transaction->cashbox_id);
        if ($transaction->type === 'deposit') {
            $cashbox->current_balance -= $transaction->amount;
        } else {
            $cashbox->current_balance += $transaction->amount;
        }
        $cashbox->save();

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', __('messages.transaction_deleted'));
    }

    // طباعة الإيصال
    public function printReceipt(Transaction $transaction)
    {
        $transaction->load(['cashbox', 'category']);

        return view('transactions.receipt', compact('transaction'));
    }

    // كشف الحساب
    public function accountStatement(Request $request)
    {
        $cashboxId = $request->cashbox_id;
        $selectedCashbox = null;
        $transactions = collect();
        $openingBalance = 0;
        $totalDeposits = 0;
        $totalWithdrawals = 0;
        $closingBalance = 0;

        if ($cashboxId) {
            $selectedCashbox = Cashbox::findOrFail($cashboxId);
            $query = Transaction::where('cashbox_id', $cashboxId)->with('category');

            // حساب الرصيد الافتتاحي (جميع الحركات قبل تاريخ البداية)
            if ($request->filled('from_date')) {
                $previousTransactions = Transaction::where('cashbox_id', $cashboxId)
                    ->whereDate('created_at', '<', $request->from_date)
                    ->get();

                $openingBalance = $selectedCashbox->opening_balance;
                foreach ($previousTransactions as $trans) {
                    if ($trans->type == 'deposit') {
                        $openingBalance += $trans->amount;
                    } else {
                        $openingBalance -= $trans->amount;
                    }
                }

                $query->whereDate('created_at', '>=', $request->from_date);
            } else {
                $openingBalance = $selectedCashbox->opening_balance;
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $transactions = $query->orderBy('created_at', 'asc')->get();

            // حساب الإجماليات
            $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
            $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
            $closingBalance = $openingBalance + $totalDeposits - $totalWithdrawals;
        }

        $cashboxes = Cashbox::all();

        return view('transactions.statement', compact(
            'selectedCashbox',
            'transactions',
            'cashboxes',
            'openingBalance',
            'totalDeposits',
            'totalWithdrawals',
            'closingBalance'
        ));
    }

    // طباعة كشف الحساب
    public function printStatement(Request $request)
    {
        $cashboxId = $request->cashbox_id;
        $selectedCashbox = Cashbox::findOrFail($cashboxId);
        $query = Transaction::where('cashbox_id', $cashboxId)->with('category');

        // حساب الرصيد الافتتاحي (جميع الحركات قبل تاريخ البداية)
        if ($request->filled('from_date')) {
            $previousTransactions = Transaction::where('cashbox_id', $cashboxId)
                ->whereDate('created_at', '<', $request->from_date)
                ->get();

            $openingBalance = $selectedCashbox->opening_balance;
            foreach ($previousTransactions as $trans) {
                if ($trans->type == 'deposit') {
                    $openingBalance += $trans->amount;
                } else {
                    $openingBalance -= $trans->amount;
                }
            }

            $query->whereDate('created_at', '>=', $request->from_date);
        } else {
            $openingBalance = $selectedCashbox->opening_balance;
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderBy('created_at', 'asc')->get();

        // حساب الإجماليات
        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
        $closingBalance = $openingBalance + $totalDeposits - $totalWithdrawals;

        return view('transactions.print-statement', compact(
            'selectedCashbox',
            'transactions',
            'openingBalance',
            'totalDeposits',
            'totalWithdrawals',
            'closingBalance'
        ));
    }
}
