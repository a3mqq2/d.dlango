<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('variants');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Stock status filter
        if ($request->filled('stock_status')) {
            $status = $request->stock_status;
            if ($status === 'in_stock') {
                $query->where(function ($q) {
                    $q->where('type', 'simple')->where('quantity', '>', 0)
                      ->orWhere('type', 'variable')->whereHas('variants', function ($vq) {
                          $vq->where('quantity', '>', 0);
                      });
                });
            } elseif ($status === 'low_stock') {
                $query->where(function ($q) {
                    $q->where('type', 'simple')->whereBetween('quantity', [1, 5])
                      ->orWhere('type', 'variable')->whereHas('variants', function ($vq) {
                          $vq->whereBetween('quantity', [1, 5]);
                      });
                });
            } elseif ($status === 'out_of_stock') {
                $query->where(function ($q) {
                    $q->where('type', 'simple')->where('quantity', 0)
                      ->orWhere('type', 'variable')->whereDoesntHave('variants', function ($vq) {
                          $vq->where('quantity', '>', 0);
                      });
                });
            }
        }

        $products = $query->latest()->paginate(20);

        // Statistics
        $totalProducts = Product::count();
        $simpleProducts = Product::where('type', 'simple')->count();
        $variableProducts = Product::where('type', 'variable')->count();

        // In-stock products
        $inStockSimple = Product::where('type', 'simple')->where('quantity', '>', 0)->count();
        $inStockVariable = Product::where('type', 'variable')
            ->whereHas('variants', function ($q) {
                $q->where('quantity', '>', 0);
            })->count();
        $inStockCount = $inStockSimple + $inStockVariable;

        // Out of stock products
        $outOfStockSimple = Product::where('type', 'simple')->where('quantity', 0)->count();
        $outOfStockVariable = Product::where('type', 'variable')
            ->whereDoesntHave('variants', function ($q) {
                $q->where('quantity', '>', 0);
            })->count();
        $outOfStockCount = $outOfStockSimple + $outOfStockVariable;

        // Low stock (1-5 items)
        $lowStockSimple = Product::where('type', 'simple')->whereBetween('quantity', [1, 5])->count();
        $lowStockVariable = Product::where('type', 'variable')
            ->whereHas('variants', function ($q) {
                $q->whereBetween('quantity', [1, 5]);
            })->count();
        $lowStockCount = $lowStockSimple + $lowStockVariable;

        // Total inventory value
        $simpleValue = Product::where('type', 'simple')
            ->selectRaw('SUM(quantity * purchase_price) as total')
            ->value('total') ?? 0;

        $variantValue = ProductVariant::selectRaw('SUM(quantity * purchase_price) as total')
            ->value('total') ?? 0;

        $totalInventoryValue = $simpleValue + $variantValue;

        return view('inventory.index', compact(
            'products',
            'totalProducts',
            'simpleProducts',
            'variableProducts',
            'inStockCount',
            'outOfStockCount',
            'lowStockCount',
            'totalInventoryValue'
        ));
    }

    public function show(Product $product)
    {
        $product->load('variants', 'purchaseInvoiceItems.purchaseInvoice');

        return view('inventory.show', compact('product'));
    }

    public function printBarcode(Request $request)
    {
        $productId = $request->get('product_id');
        $variantId = $request->get('variant_id');
        $quantity = $request->get('quantity', 1);

        if ($variantId) {
            $variant = ProductVariant::with('product')->findOrFail($variantId);
            $item = [
                'code' => $variant->code,
                'name' => $variant->product->name . ' - ' . $variant->variant_name,
                'price' => $variant->selling_price,
                'sku' => $variant->product->sku,
            ];
        } else {
            $product = Product::findOrFail($productId);
            $item = [
                'code' => $product->code,
                'name' => $product->name,
                'price' => $product->selling_price,
                'sku' => $product->sku,
            ];
        }

        return view('inventory.print-barcode', compact('item', 'quantity'));
    }

    public function barcodeForm(Product $product)
    {
        $product->load('variants');

        return view('inventory.barcode-form', compact('product'));
    }

    public function bulkBarcode(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:product,variant'],
            'items.*.id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $barcodes = [];

        foreach ($validated['items'] as $item) {
            if ($item['type'] === 'product') {
                $product = Product::find($item['id']);
                if ($product) {
                    for ($i = 0; $i < $item['quantity']; $i++) {
                        $barcodes[] = [
                            'code' => $product->code,
                            'name' => $product->name,
                            'price' => $product->selling_price,
                            'sku' => $product->sku,
                        ];
                    }
                }
            } else {
                $variant = ProductVariant::with('product')->find($item['id']);
                if ($variant) {
                    for ($i = 0; $i < $item['quantity']; $i++) {
                        $barcodes[] = [
                            'code' => $variant->code,
                            'name' => $variant->product->name . ' - ' . $variant->variant_name,
                            'price' => $variant->selling_price,
                            'sku' => $variant->product->sku,
                        ];
                    }
                }
            }
        }

        return view('inventory.print-bulk-barcode', compact('barcodes'));
    }

public function printBarcodesRaw(Request $request)
{
    $barcodes = $request->input('barcodes');

    $tspl = "SIZE 38 mm,25 mm\n";
    $tspl .= "GAP 2 mm,0 mm\n";
    $tspl .= "DENSITY 7\n";
    $tspl .= "SPEED 2\n";
    $tspl .= "DIRECTION 1\n";
    $tspl .= "REFERENCE 0,0\n";
    $tspl .= "CLS\n";

    foreach ($barcodes as $item) {
        $name = substr(preg_replace('/[^A-Za-z0-9 \\-]/', '', $item['name']), 0, 22);
        $code = $item['code'];
        $price = number_format($item['price'], 2);

        $tspl .= "TEXT 2,2,\"0\",0,1,1,\"$name\"\n";
        $tspl .= "BARCODE 2,12,\"128\",18,1,0,2,2,\"$code\"\n";
        $tspl .= "TEXT 2,32,\"0\",0,1,1,\"$code\"\n";
        $tspl .= "TEXT 2,40,\"0\",0,1,1,\"$price LYD\"\n";
        $tspl .= "PRINT 1,1\n";
        $tspl .= "CLS\n";
    }

    file_put_contents("\\\\localhost\\EML-200L", $tspl);

    return response()->json([
        'status' => 'ok',
        'count' => count($barcodes)
    ]);
}

}
