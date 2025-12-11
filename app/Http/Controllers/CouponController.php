<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of coupons
     */
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->valid();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('end_date', '<', now());
                    break;
                case 'scheduled':
                    $query->where('start_date', '>', now());
                    break;
            }
        }

        $coupons = $query->latest()->paginate(15);

        return view('coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new coupon
     */
    public function create()
    {
        return view('coupons.create');
    }

    /**
     * Store a newly created coupon
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_customer' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
        ]);

        // Validate percentage value
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => __('messages.percentage_max_100')])->withInput();
        }

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Coupon::create($validated);

        return redirect()->route('coupons.index')
            ->with('success', __('messages.coupon_created'));
    }

    /**
     * Display the specified coupon
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['usages.customer', 'usages.sale']);
        return view('coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(Coupon $coupon)
    {
        return view('coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified coupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code,' . $coupon->id],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:fixed,percentage'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_customer' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
        ]);

        // Validate percentage value
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => __('messages.percentage_max_100')])->withInput();
        }

        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $coupon->update($validated);

        return redirect()->route('coupons.index')
            ->with('success', __('messages.coupon_updated'));
    }

    /**
     * Remove the specified coupon
     */
    public function destroy(Coupon $coupon)
    {
        // Check if coupon has been used
        if ($coupon->used_count > 0) {
            return back()->with('error', __('messages.cannot_delete_used_coupon'));
        }

        $coupon->delete();

        return redirect()->route('coupons.index')
            ->with('success', __('messages.coupon_deleted'));
    }

    /**
     * Toggle coupon status
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return back()->with('success', __('messages.status_updated'));
    }

    /**
     * Generate random coupon code (AJAX)
     */
    public function generateCode()
    {
        return response()->json([
            'code' => Coupon::generateCode()
        ]);
    }

    /**
     * Validate coupon for POS (AJAX)
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'order_total' => ['required', 'numeric', 'min:0'],
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => __('messages.coupon_not_found'),
            ], 404);
        }

        $validationMessage = $coupon->getValidationMessage(
            $request->customer_id,
            $request->order_total
        );

        if ($validationMessage) {
            return response()->json([
                'success' => false,
                'message' => $validationMessage,
            ], 400);
        }

        $discount = $coupon->calculateDiscount($request->order_total);

        return response()->json([
            'success' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount_text' => $coupon->discount_text,
            ],
            'discount' => $discount,
            'message' => __('messages.coupon_applied'),
        ]);
    }
}
