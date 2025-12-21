<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\Cashbox;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseInvoice::with(['supplier', 'cashbox']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $invoices = $query->latest()->paginate(10);
        $suppliers = Supplier::all();

        return view('purchase-invoices.index', compact('invoices', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::get();
        $cashboxes = Cashbox::all();
        $nextInvoiceNumber = PurchaseInvoice::generateInvoiceNumber();

        return view('purchase-invoices.create', compact('suppliers', 'cashboxes', 'nextInvoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'invoice_date' => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,credit'],
            'cashbox_id' => ['required_if:payment_method,cash', 'nullable', 'exists:cashboxes,id'],
            'notes' => ['nullable', 'string'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.type' => ['required', 'in:simple,variable'],
            'products.*.code' => ['required', 'string', 'size:4'],
            'products.*.name' => ['required', 'string'],
            'products.*.sku' => ['nullable', 'string'],
            'products.*.image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp'],
            'products.*.quantity' => ['required_if:products.*.type,simple', 'nullable', 'integer', 'min:1'],
            'products.*.purchase_price' => ['required_if:products.*.type,simple', 'nullable', 'numeric', 'min:0'],
            'products.*.selling_price' => ['required_if:products.*.type,simple', 'nullable', 'numeric', 'min:0'],
            'products.*.variants' => ['required_if:products.*.type,variable', 'nullable', 'array', 'min:1'],
            'products.*.variants.*.code' => ['required', 'string', 'size:4'],
            'products.*.variants.*.variant_name' => ['required', 'string'],
            'products.*.variants.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.variants.*.purchase_price' => ['required', 'numeric', 'min:0'],
            'products.*.variants.*.selling_price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $totalProfit = 0;

            // Create invoice
            $invoice = PurchaseInvoice::create([
                'invoice_number' => PurchaseInvoice::generateInvoiceNumber(),
                'supplier_id' => $validated['supplier_id'],
                'invoice_date' => $validated['invoice_date'],
                'total_amount' => 0, // Will be updated
                'total_profit' => 0, // Will be updated
                'payment_method' => $validated['payment_method'],
                'cashbox_id' => $validated['cashbox_id'] ?? null,
                'status' => 'pending_shipment',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Process products
            $productsData = $request->input('products');
            $productFiles = $request->file('products');

            foreach ($validated['products'] as $index => $productData) {
                // Handle image upload
                $imagePath = null;
                if (isset($productFiles[$index]['image'])) {
                    $imagePath = $productFiles[$index]['image']->store('products', 'public');
                }

                if ($productData['type'] === 'simple') {
                    // Simple product - check if SKU exists
                    $product = null;
                    if (!empty($productData['sku'])) {
                        $product = Product::where('sku', $productData['sku'])->first();
                    }

                    if ($product) {
                        // Update existing product prices and image if provided
                        $product->update([
                            'purchase_price' => $productData['purchase_price'],
                            'selling_price' => $productData['selling_price'],
                            'profit_per_unit' => $productData['selling_price'] - $productData['purchase_price'],
                            'image' => $imagePath ?? $product->image,
                        ]);
                    } else {
                        // Create new product
                        $product = Product::create([
                            'code' => $productData['code'],
                            'name' => $productData['name'],
                            'sku' => $productData['sku'] ?? null,
                            'type' => 'simple',
                            'quantity' => 0, // Will be added on receive
                            'purchase_price' => $productData['purchase_price'],
                            'selling_price' => $productData['selling_price'],
                            'profit_per_unit' => $productData['selling_price'] - $productData['purchase_price'],
                            'image' => $imagePath,
                        ]);
                    }

                    $profitPerUnit = $productData['selling_price'] - $productData['purchase_price'];
                    $itemTotalProfit = $profitPerUnit * $productData['quantity'];
                    $subtotal = $productData['purchase_price'] * $productData['quantity'];

                    PurchaseInvoiceItem::create([
                        'purchase_invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'product_variant_id' => null,
                        'quantity' => $productData['quantity'],
                        'purchase_price' => $productData['purchase_price'],
                        'selling_price' => $productData['selling_price'],
                        'profit_per_unit' => $profitPerUnit,
                        'total_profit' => $itemTotalProfit,
                        'subtotal' => $subtotal,
                    ]);

                    $totalAmount += $subtotal;
                    $totalProfit += $itemTotalProfit;
                } else {
                    // Variable product
                    $product = Product::create([
                        'code' => $productData['code'],
                        'name' => $productData['name'],
                        'sku' => $productData['sku'] ?? null,
                        'type' => 'variable',
                        'quantity' => 0,
                        'image' => $imagePath,
                    ]);

                    foreach ($productData['variants'] as $variantData) {
                        $profitPerUnit = $variantData['selling_price'] - $variantData['purchase_price'];

                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'code' => $variantData['code'],
                            'variant_name' => $variantData['variant_name'],
                            'quantity' => 0, // Will be added on receive
                            'purchase_price' => $variantData['purchase_price'],
                            'selling_price' => $variantData['selling_price'],
                            'profit_per_unit' => $profitPerUnit,
                        ]);

                        $itemTotalProfit = $profitPerUnit * $variantData['quantity'];
                        $subtotal = $variantData['purchase_price'] * $variantData['quantity'];

                        PurchaseInvoiceItem::create([
                            'purchase_invoice_id' => $invoice->id,
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'quantity' => $variantData['quantity'],
                            'purchase_price' => $variantData['purchase_price'],
                            'selling_price' => $variantData['selling_price'],
                            'profit_per_unit' => $profitPerUnit,
                            'total_profit' => $itemTotalProfit,
                            'subtotal' => $subtotal,
                        ]);

                        $totalAmount += $subtotal;
                        $totalProfit += $itemTotalProfit;
                    }
                }
            }

            // Update invoice totals
            $invoice->update([
                'total_amount' => $totalAmount,
                'total_profit' => $totalProfit,
            ]);

            // Handle payment based on payment method
            $supplier = Supplier::find($validated['supplier_id']);

            if ($validated['payment_method'] === 'cash') {
                // Cash payment - only deduct from cashbox, supplier account NOT affected
                $cashbox = Cashbox::find($validated['cashbox_id']);
                $cashbox->current_balance -= $totalAmount;
                $cashbox->save();

                // Create withdrawal transaction for cashbox records (without supplier_id)
                Transaction::create([
                    'cashbox_id' => $validated['cashbox_id'],
                    'supplier_id' => null, // Not linked to supplier - cash payment is immediate
                    'recipient_name' => $supplier->name,
                    'recipient_id' => $supplier->phone,
                    'type' => 'withdrawal',
                    'amount' => $totalAmount,
                    'description' => __('messages.purchase_payment_for_invoice') . ' #' . $invoice->invoice_number,
                ]);
            } else {
                // Credit payment - affects supplier balance (we owe supplier)
                // No cashbox needed for credit purchases - only affects supplier balance
                Transaction::create([
                    'cashbox_id' => null, // No cashbox for credit purchases
                    'supplier_id' => $supplier->id, // Linked to supplier - credit affects balance
                    'recipient_name' => $supplier->name,
                    'recipient_id' => $supplier->phone,
                    'type' => 'withdrawal',
                    'amount' => $totalAmount,
                    'description' => __('messages.credit_purchase_invoice') . ' #' . $invoice->invoice_number,
                ]);

                // Recalculate supplier balance only for credit purchases
                $supplier->recalculateBalance();
            }

            DB::commit();

            return redirect()->route('purchase-invoices.show', $invoice)
                ->with('success', __('messages.purchase_invoice_created'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.error_creating_invoice') . ': ' . $e->getMessage());
        }
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['supplier', 'cashbox', 'items.product', 'items.variant']);
        $invoice = $purchaseInvoice;

        return view('purchase-invoices.show', compact('invoice'));
    }

    public function receive(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status !== 'pending_shipment') {
            return redirect()->back()->with('error', __('messages.invoice_already_processed'));
        }

        DB::beginTransaction();

        try {
            // Add items to inventory
            foreach ($purchaseInvoice->items as $item) {
                if ($item->product_variant_id) {
                    // Variable product
                    $variant = ProductVariant::find($item->product_variant_id);
                    $variant->quantity += $item->quantity;
                    $variant->save();
                } else {
                    // Simple product
                    $product = Product::find($item->product_id);
                    $product->quantity += $item->quantity;
                    $product->save();
                }
            }

            // Update invoice status
            $purchaseInvoice->update(['status' => 'received']);

            DB::commit();

            return redirect()->route('purchase-invoices.show', $purchaseInvoice)
                ->with('success', __('messages.purchase_invoice_received'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('messages.error_receiving_invoice') . ': ' . $e->getMessage());
        }
    }

    public function cancel(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status === 'received') {
            return redirect()->back()->with('error', __('messages.cannot_cancel_received_invoice'));
        }

        DB::beginTransaction();

        try {
            $supplier = Supplier::find($purchaseInvoice->supplier_id);

            if ($purchaseInvoice->payment_method === 'cash') {
                // Reverse cashbox withdrawal - supplier account NOT affected
                $cashbox = Cashbox::find($purchaseInvoice->cashbox_id);
                $cashbox->current_balance += $purchaseInvoice->total_amount;
                $cashbox->save();

                // Create deposit transaction for cashbox records (without supplier_id)
                Transaction::create([
                    'cashbox_id' => $purchaseInvoice->cashbox_id,
                    'supplier_id' => null, // Not linked to supplier
                    'recipient_name' => $supplier->name,
                    'recipient_id' => $supplier->phone,
                    'type' => 'deposit',
                    'amount' => $purchaseInvoice->total_amount,
                    'description' => __('messages.cancel_purchase_invoice') . ' #' . $purchaseInvoice->invoice_number,
                ]);
            } else {
                // Create deposit transaction to reverse the credit (cancel the debt)
                // No cashbox needed for credit cancellations - only affects supplier balance

                Transaction::create([
                    'cashbox_id' => null, // No cashbox for credit cancellations
                    'supplier_id' => $supplier->id, // Linked to supplier - affects balance
                    'recipient_name' => $supplier->name,
                    'recipient_id' => $supplier->phone,
                    'type' => 'deposit',
                    'amount' => $purchaseInvoice->total_amount,
                    'description' => __('messages.cancel_credit_invoice') . ' #' . $purchaseInvoice->invoice_number,
                ]);

                // Recalculate supplier balance only for credit purchases
                $supplier->recalculateBalance();
            }

            // Update status
            $purchaseInvoice->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('purchase-invoices.show', $purchaseInvoice)
                ->with('success', __('messages.purchase_invoice_cancelled'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', __('messages.error_cancelling_invoice') . ': ' . $e->getMessage());
        }
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        if ($purchaseInvoice->status === 'received') {
            return redirect()->back()->with('error', __('messages.cannot_delete_received_invoice'));
        }

        $purchaseInvoice->delete();

        return redirect()->route('purchase-invoices.index')
            ->with('success', __('messages.purchase_invoice_deleted'));
    }

    // API endpoint for supplier search with creation
    public function searchSuppliers(Request $request)
    {
        $search = $request->get('q', '');

        $suppliers = Supplier::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($suppliers);
    }

    // API endpoint to create supplier on the fly
    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:suppliers,name'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $supplier = Supplier::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'balance' => 0,
            'is_active' => true,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'message' => __('messages.supplier_created'),
            ]);
        }

        return redirect()->back()->with('success', __('messages.supplier_created'));
    }
}
