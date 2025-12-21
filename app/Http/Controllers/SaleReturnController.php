<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Cashbox;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    /**
     * Display returns list
     */
    public function index(Request $request)
    {
        $query = SaleReturn::with(['sale', 'customer', 'user', 'items']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhereHas('sale', function ($sq) use ($search) {
                      $sq->where('invoice_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }

        $returns = $query->latest()->paginate(15);

        return view('returns.index', compact('returns'));
    }

    /**
     * Show create return form
     */
    public function create()
    {
        $cashboxes = Cashbox::all();
        return view('returns.create', compact('cashboxes'));
    }

    /**
     * Search sale by invoice number
     */
    public function searchSale(Request $request)
    {
        $invoiceNumber = $request->get('invoice_number');

        $sale = Sale::with(['customer', 'items.product', 'items.variant'])
            ->where('invoice_number', $invoiceNumber)
            ->where('status', 'completed')
            ->first();

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => __('messages.sale_not_found'),
            ], 404);
        }

        // Calculate returnable quantities
        $items = $sale->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->product_variant_id,
                'name' => $item->display_name,
                'code' => $item->variant ? $item->variant->code : $item->product->code,
                'quantity' => $item->quantity,
                'returned_quantity' => $item->returned_quantity,
                'returnable_quantity' => $item->returnable_quantity,
                'unit_price' => $item->unit_price,
            ];
        })->filter(function ($item) {
            return $item['returnable_quantity'] > 0;
        })->values();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.all_items_returned'),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'sale_date' => $sale->sale_date->format('Y-m-d H:i'),
                'customer' => $sale->customer->name,
                'customer_id' => $sale->customer_id,
                'total_amount' => $sale->total_amount,
                'payment_method' => $sale->payment_method,
            ],
            'items' => $items,
        ]);
    }

    /**
     * Process return
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'cashbox_id' => ['required_if:refund_method,cash', 'nullable', 'exists:cashboxes,id'],
            'refund_method' => ['required', 'in:cash,credit'],
            'reason' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sale_item_id' => ['required', 'exists:sale_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $sale = Sale::with('customer', 'items')->find($validated['sale_id']);

        DB::beginTransaction();

        try {
            // Validate quantities
            $totalAmount = 0;
            $returnItems = [];

            foreach ($validated['items'] as $item) {
                $saleItem = $sale->items->find($item['sale_item_id']);

                if (!$saleItem) {
                    throw new \Exception(__('messages.invalid_item'));
                }

                if ($item['quantity'] > $saleItem->returnable_quantity) {
                    throw new \Exception(__('messages.return_quantity_exceeds', ['item' => $saleItem->display_name]));
                }

                $itemTotal = $saleItem->unit_price * $item['quantity'];
                $totalAmount += $itemTotal;

                $returnItems[] = [
                    'sale_item_id' => $saleItem->id,
                    'product_id' => $saleItem->product_id,
                    'product_variant_id' => $saleItem->product_variant_id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $saleItem->unit_price,
                    'subtotal' => $itemTotal,
                ];
            }

            // Create return record
            $saleReturn = SaleReturn::create([
                'return_number' => SaleReturn::generateReturnNumber(),
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'user_id' => auth()->id(),
                'cashbox_id' => $validated['cashbox_id'] ?? null,
                'return_date' => now(),
                'total_amount' => $totalAmount,
                'refund_method' => $validated['refund_method'],
                'status' => 'completed',
                'reason' => $validated['reason'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create return items and restore inventory
            foreach ($returnItems as $item) {
                SaleReturnItem::create([
                    'sale_return_id' => $saleReturn->id,
                    ...$item,
                ]);

                // Restore inventory
                if ($item['product_variant_id']) {
                    ProductVariant::find($item['product_variant_id'])
                        ->increment('quantity', $item['quantity']);
                } else {
                    Product::find($item['product_id'])
                        ->increment('quantity', $item['quantity']);
                }
            }

            // Handle refund based on refund method
            $customer = $sale->customer;

            if ($validated['refund_method'] === 'cash') {
                // Cash refund - only deduct from cashbox, customer account NOT affected
                $cashbox = Cashbox::find($validated['cashbox_id']);
                $cashbox->decrement('current_balance', $totalAmount);

                // Create withdrawal transaction for cashbox records (without customer_id)
                Transaction::create([
                    'cashbox_id' => $cashbox->id,
                    'customer_id' => null, // Not linked to customer - cash refund is immediate
                    'recipient_name' => $customer->name,
                    'recipient_id' => $customer->phone,
                    'type' => 'withdrawal',
                    'amount' => $totalAmount,
                    'description' => __('messages.return_refund') . ' #' . $saleReturn->return_number,
                ]);
            } else {
                // Credit refund - affects customer balance (we owe customer or reduce their debt)
                // Use default cashbox for tracking credit transactions
                $defaultCashbox = Cashbox::first();

                Transaction::create([
                    'cashbox_id' => $defaultCashbox->id,
                    'customer_id' => $customer->id, // Linked to customer - credit affects balance
                    'recipient_name' => $customer->name,
                    'recipient_id' => $customer->phone,
                    'type' => 'deposit', // Deposit to customer account (reduces their debt or gives credit)
                    'amount' => $totalAmount,
                    'description' => __('messages.credit_return_invoice') . ' #' . $saleReturn->return_number,
                ]);

                // Recalculate customer balance for credit returns
                $customer->recalculateBalance();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.return_completed'),
                'return' => $saleReturn->load('items.product', 'items.variant'),
                'return_number' => $saleReturn->return_number,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show return details
     */
    public function show(SaleReturn $return)
    {
        $return->load('sale', 'customer', 'user', 'cashbox', 'items.product', 'items.variant');
        return view('returns.show', compact('return'));
    }

    /**
     * Print return receipt
     */
    public function receipt(SaleReturn $return)
    {
        $return->load('sale', 'customer', 'user', 'cashbox', 'items.product', 'items.variant');
        return view('returns.receipt', compact('return'));
    }
}
