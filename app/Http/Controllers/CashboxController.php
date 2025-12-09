<?php

namespace App\Http\Controllers;

use App\Models\Cashbox;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CashboxController extends Controller
{
    public function index(Request $request)
    {
        $query = Cashbox::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $cashboxes = $query->latest()->paginate(10);

        return view('cashboxes.index', compact('cashboxes'));
    }

    public function create()
    {
        return view('cashboxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
        ]);

        // إنشاء الخزينة
        $cashbox = Cashbox::create([
            'name' => $validated['name'],
            'opening_balance' => $validated['opening_balance'],
            'current_balance' => $validated['opening_balance'],
        ]);

        // إنشاء حركة إيداع للرصيد الافتتاحي إذا كان أكبر من صفر
        if ($validated['opening_balance'] > 0) {
            // نحتاج إلى تصنيف افتراضي للرصيد الافتتاحي
            $openingCategory = \App\Models\TransactionCategory::firstOrCreate(
                ['name' => __('messages.opening_balance')]
            );

            Transaction::create([
                'cashbox_id' => $cashbox->id,
                'transaction_category_id' => $openingCategory->id,
                'recipient_name' => __('messages.system'),
                'type' => 'deposit',
                'amount' => $validated['opening_balance'],
                'description' => __('messages.opening_balance_transaction'),
            ]);
        }

        return redirect()->route('cashboxes.index')
            ->with('success', __('messages.cashbox_created'));
    }

    public function show(Cashbox $cashbox)
    {
        // جلب آخر 10 حركات مالية للخزينة
        $recentTransactions = Transaction::where('cashbox_id', $cashbox->id)
            ->with('category')
            ->latest()
            ->limit(10)
            ->get();

        return view('cashboxes.show', compact('cashbox', 'recentTransactions'));
    }

    public function edit(Cashbox $cashbox)
    {
        return view('cashboxes.edit', compact('cashbox'));
    }

    public function update(Request $request, Cashbox $cashbox)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $cashbox->update($validated);

        return redirect()->route('cashboxes.index')
            ->with('success', __('messages.cashbox_updated'));
    }

    public function destroy(Cashbox $cashbox)
    {
        $cashbox->delete();

        return redirect()->route('cashboxes.index')
            ->with('success', __('messages.cashbox_deleted'));
    }
}
