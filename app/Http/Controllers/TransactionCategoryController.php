<?php

namespace App\Http\Controllers;

use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        // Show only user categories (not system categories)
        $categories = TransactionCategory::userCategories()
            ->withCount('transactions')
            ->latest()
            ->paginate(10);

        return view('transaction_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:transaction_categories,name'],
        ]);

        $category = TransactionCategory::create($validated);

        // إذا كان الطلب AJAX، نرجع JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => __('messages.category_created'),
            ]);
        }

        return redirect()->route('transaction-categories.index')
            ->with('success', __('messages.category_created'));
    }

    public function update(Request $request, TransactionCategory $transactionCategory)
    {
        // Prevent updating system categories
        if ($transactionCategory->is_system) {
            return redirect()->route('transaction-categories.index')
                ->with('error', __('messages.cannot_modify_system_category'));
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:transaction_categories,name,' . $transactionCategory->id],
        ]);

        $transactionCategory->update($validated);

        return redirect()->route('transaction-categories.index')
            ->with('success', __('messages.category_updated'));
    }

    public function destroy(TransactionCategory $transactionCategory)
    {
        // Prevent deleting system categories
        if ($transactionCategory->is_system) {
            return redirect()->route('transaction-categories.index')
                ->with('error', __('messages.cannot_delete_system_category'));
        }

        $transactionCategory->delete();

        return redirect()->route('transaction-categories.index')
            ->with('success', __('messages.category_deleted'));
    }
}
