<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customer;
use App\Models\Cashbox;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    /**
     * Display POS interface
     */
    public function index()
    {
        $customers = Customer::active()->orderBy('is_default', 'desc')->orderBy('name')->get();
        $cashboxes = Cashbox::all();
        $defaultCustomer = Customer::getDefaultCustomer();

        return view('pos.index', compact('customers', 'cashboxes', 'defaultCustomer'));
    }

    /**
     * Search products for POS
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');

        $products = Product::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
        })
        ->where('quantity', '>', 0)
        ->orWhereHas('variants', function ($q) use ($search) {
            $q->where('quantity', '>', 0);
        })
        ->with(['variants' => function ($q) {
            $q->where('quantity', '>', 0);
        }])
        ->limit(20)
        ->get()
        ->map(function ($product) {
            if ($product->type === 'simple') {
                return [
                    'id' => $product->id,
                    'variant_id' => null,
                    'code' => $product->code,
                    'name' => $product->name,
                    'price' => $product->selling_price,
                    'quantity' => $product->quantity,
                    'image' => $product->image_url,
                    'type' => 'simple',
                ];
            } else {
                return $product->variants->map(function ($variant) use ($product) {
                    return [
                        'id' => $product->id,
                        'variant_id' => $variant->id,
                        'code' => $variant->code,
                        'name' => $product->name . ' - ' . $variant->variant_name,
                        'price' => $variant->selling_price,
                        'quantity' => $variant->quantity,
                        'image' => $product->image_url,
                        'type' => 'variable',
                    ];
                });
            }
        })
        ->flatten(1)
        ->filter()
        ->values();

        return response()->json($products);
    }

    /**
     * Get all products for grid display
     */
    public function getProducts()
    {
        $products = Product::with(['variants' => function ($q) {
            $q->where('quantity', '>', 0);
        }])
        ->get()
        ->map(function ($product) {
            if ($product->type === 'simple') {
                if ($product->quantity <= 0) return null;
                return [
                    'id' => $product->id,
                    'variant_id' => null,
                    'code' => $product->code,
                    'name' => $product->name,
                    'price' => $product->selling_price,
                    'quantity' => $product->quantity,
                    'image' => $product->image_url,
                    'type' => 'simple',
                    'variants' => [],
                ];
            } else {
                // For variable products, show the parent product with variants array
                $variants = $product->variants->map(function ($variant) use ($product) {
                    return [
                        'id' => $product->id,
                        'variant_id' => $variant->id,
                        'code' => $variant->code,
                        'name' => $variant->variant_name,
                        'full_name' => $product->name . ' - ' . $variant->variant_name,
                        'price' => $variant->selling_price,
                        'quantity' => $variant->quantity,
                    ];
                })->toArray();

                // Only show if has variants with stock
                if (empty($variants)) return null;

                // Calculate total quantity and price range
                $totalQuantity = array_sum(array_column($variants, 'quantity'));
                $prices = array_column($variants, 'price');
                $minPrice = min($prices);
                $maxPrice = max($prices);

                return [
                    'id' => $product->id,
                    'variant_id' => null,
                    'code' => $product->code,
                    'name' => $product->name,
                    'price' => $minPrice,
                    'price_range' => $minPrice != $maxPrice ? [$minPrice, $maxPrice] : null,
                    'quantity' => $totalQuantity,
                    'image' => $product->image_url,
                    'type' => 'variable',
                    'variants' => $variants,
                ];
            }
        })
        ->filter()
        ->values();

        return response()->json($products);
    }

    /**
     * Process sale
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'cashbox_id' => ['required_if:payment_method,cash', 'nullable', 'exists:cashboxes,id'],
            'payment_method' => ['required', 'in:cash,credit'],
            'payment_type' => ['nullable', 'in:cash,bank_transfer'],
            'bank_account' => ['required_if:payment_type,bank_transfer', 'nullable', 'string', 'max:255'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'in:fixed,percentage'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'coupon_id' => ['nullable', 'exists:coupons,id'],
            'coupon_discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Prevent credit sales for default (walk-in) customer
        $customer = Customer::find($validated['customer_id']);
        if ($customer->is_default && $validated['payment_method'] === 'credit') {
            return response()->json([
                'success' => false,
                'message' => __('messages.credit_not_allowed_for_default_customer'),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $itemSubtotal = ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0);
                $subtotal += $itemSubtotal;
            }

            // Calculate discount
            $discountAmount = 0;
            if (!empty($validated['discount'])) {
                if (($validated['discount_type'] ?? 'fixed') === 'percentage') {
                    $discountAmount = ($subtotal * $validated['discount']) / 100;
                } else {
                    $discountAmount = $validated['discount'];
                }
            }

            // Process coupon discount
            $couponDiscount = $validated['coupon_discount'] ?? 0;
            $couponId = $validated['coupon_id'] ?? null;

            // Validate coupon if provided
            if ($couponId) {
                $coupon = Coupon::find($couponId);
                if (!$coupon || !$coupon->canBeUsedByCustomer($validated['customer_id'])) {
                    throw new \Exception(__('messages.coupon_invalid'));
                }
                // Recalculate coupon discount to ensure it's accurate
                $couponDiscount = $coupon->calculateDiscount($subtotal - $discountAmount);
            }

            $totalAmount = $subtotal - $discountAmount - $couponDiscount;
            $paidAmount = $validated['paid_amount'] ?? ($validated['payment_method'] === 'cash' ? $totalAmount : 0);
            $remainingAmount = $totalAmount - $paidAmount;

            // Create sale
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'cashbox_id' => $validated['cashbox_id'] ?? null,
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'discount_type' => $validated['discount_type'] ?? 'fixed',
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_method' => $validated['payment_method'],
                'payment_type' => $validated['payment_type'] ?? null,
                'bank_account' => $validated['bank_account'] ?? null,
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
                'coupon_id' => $couponId,
                'coupon_discount' => $couponDiscount,
            ]);

            // Record coupon usage
            if ($couponId && $coupon) {
                $coupon->recordUsage($validated['customer_id'], $sale->id, $couponDiscount);
            }

            // Create sale items and update inventory
            foreach ($validated['items'] as $item) {
                $itemDiscount = $item['discount'] ?? 0;
                $itemSubtotal = ($item['price'] * $item['quantity']) - $itemDiscount;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                ]);

                // Update inventory
                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::find($item['variant_id']);
                    $variant->decrement('quantity', $item['quantity']);
                } else {
                    $product = Product::find($item['product_id']);
                    $product->decrement('quantity', $item['quantity']);
                }
            }

            // Handle payment based on payment method
            $customer = Customer::find($validated['customer_id']);

            if ($validated['payment_method'] === 'cash' && $paidAmount > 0) {
                // Cash payment - only add to cashbox, customer account NOT affected
                $cashbox = Cashbox::find($validated['cashbox_id']);
                $cashbox->increment('current_balance', $paidAmount);

                // Create deposit transaction for cashbox records (without customer_id)
                $saleCategory = TransactionCategory::getSystemCategory(__('messages.sales_revenue'));
                Transaction::create([
                    'cashbox_id' => $cashbox->id,
                    'customer_id' => null, // Not linked to customer - cash payment is immediate
                    'transaction_category_id' => $saleCategory->id,
                    'recipient_name' => $customer->name,
                    'recipient_id' => $customer->phone,
                    'type' => 'deposit',
                    'amount' => $paidAmount,
                    'description' => __('messages.sale_payment') . ' #' . $sale->invoice_number,
                ]);
            } elseif ($validated['payment_method'] === 'credit') {
                // Credit payment - affects customer balance (customer owes us)
                $creditCategory = TransactionCategory::getSystemCategory(__('messages.credit_sale'));

                // Use default cashbox for tracking credit transactions
                $defaultCashbox = Cashbox::first();

                Transaction::create([
                    'cashbox_id' => $defaultCashbox->id,
                    'customer_id' => $customer->id, // Linked to customer - credit affects balance
                    'transaction_category_id' => $creditCategory->id,
                    'recipient_name' => $customer->name,
                    'recipient_id' => $customer->phone,
                    'type' => 'withdrawal', // We "withdraw" goods to give to customer on credit
                    'amount' => $totalAmount,
                    'description' => __('messages.credit_sale_invoice') . ' #' . $sale->invoice_number,
                ]);

                // Recalculate customer balance only for credit sales
                $customer->recalculateBalance();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.sale_completed'),
                'sale' => $sale->load('items.product', 'items.variant', 'customer'),
                'invoice_number' => $sale->invoice_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('messages.error_processing_sale') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sale receipt
     */
    public function receipt()
    {
        $sale = Sale::find(request('sale_id'));
        $sale->load('items.product', 'items.variant', 'customer', 'user', 'cashbox');
        return view('pos.receipt', compact('sale'));
    }

    /**
     * Sales history
     */
    public function history(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->latest()->paginate(15);

        return view('pos.history', compact('sales'));
    }

    /**
     * Show sale details
     */
    public function show(Sale $sale)
    {
        $sale->load('items.product', 'items.variant', 'customer', 'user', 'cashbox');
        return view('pos.show', compact('sale'));
    }

    /**
     * Store a new customer from POS
     */
    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'balance' => 0,
            'is_default' => false,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('messages.customer_created'),
            'customer' => $customer,
        ]);
    }

    /**
     * Print A4 invoice
     */
    public function invoice(Sale $sale)
    {
        $sale->load('items.product', 'items.variant', 'customer', 'user', 'cashbox');
        return view('pos.invoice', compact('sale'));
    }
}
