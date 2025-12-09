<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CashboxController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ReportsController;


Route::redirect('/', '/home');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Language switching route
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch');

Route::middleware('auth')->group(function () {
    Route::get('/home', [AuthController::class, 'home'])->name('home');

    // Profile routes
    Route::get('profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Admin only routes (Users management stays admin-only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Routes with permission checks
    // Suppliers
    Route::middleware('permission:suppliers.create')->group(function () {
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    });
    Route::middleware('permission:suppliers.view')->group(function () {
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('suppliers/{supplier}/transactions', [SupplierController::class, 'transactions'])->name('suppliers.transactions');
        Route::get('suppliers/{supplier}/statement', [SupplierController::class, 'accountStatement'])->name('suppliers.statement');
        Route::get('suppliers/{supplier}/statement/print', [SupplierController::class, 'printStatement'])->name('suppliers.statement.print');
    });
    Route::middleware('permission:suppliers.edit')->group(function () {
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    });
    Route::middleware('permission:suppliers.delete')->group(function () {
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });
    Route::middleware('permission:suppliers.transactions')->group(function () {
        Route::get('suppliers/{supplier}/transactions/create', [SupplierController::class, 'addTransaction'])->name('suppliers.transactions.create');
        Route::post('suppliers/{supplier}/transactions', [SupplierController::class, 'storeTransaction'])->name('suppliers.transactions.store');
    });

    // Purchase Management
    Route::middleware('permission:purchases.create')->group(function () {
        Route::get('purchase-invoices/create', [PurchaseInvoiceController::class, 'create'])->name('purchase-invoices.create');
        Route::post('purchase-invoices', [PurchaseInvoiceController::class, 'store'])->name('purchase-invoices.store');
        Route::get('api/suppliers/search', [PurchaseInvoiceController::class, 'searchSuppliers'])->name('api.suppliers.search');
        Route::post('api/suppliers/store', [PurchaseInvoiceController::class, 'storeSupplier'])->name('api.suppliers.store');
    });
    Route::middleware('permission:purchases.view')->group(function () {
        Route::get('purchase-invoices', [PurchaseInvoiceController::class, 'index'])->name('purchase-invoices.index');
        Route::get('purchase-invoices/{purchaseInvoice}', [PurchaseInvoiceController::class, 'show'])->name('purchase-invoices.show');
    });
    Route::middleware('permission:purchases.edit')->group(function () {
        Route::get('purchase-invoices/{purchaseInvoice}/edit', [PurchaseInvoiceController::class, 'edit'])->name('purchase-invoices.edit');
        Route::put('purchase-invoices/{purchaseInvoice}', [PurchaseInvoiceController::class, 'update'])->name('purchase-invoices.update');
    });
    Route::middleware('permission:purchases.delete')->group(function () {
        Route::delete('purchase-invoices/{purchaseInvoice}', [PurchaseInvoiceController::class, 'destroy'])->name('purchase-invoices.destroy');
        Route::post('purchase-invoices/{purchaseInvoice}/cancel', [PurchaseInvoiceController::class, 'cancel'])->name('purchase-invoices.cancel');
    });
    Route::middleware('permission:purchases.receive')->group(function () {
        Route::post('purchase-invoices/{purchaseInvoice}/receive', [PurchaseInvoiceController::class, 'receive'])->name('purchase-invoices.receive');
    });

    // Financial Management
    Route::middleware('permission:finance.cashboxes')->group(function () {
        Route::resource('cashboxes', CashboxController::class);
    });
    Route::middleware('permission:finance.transactions')->group(function () {
        Route::resource('transactions', TransactionController::class);
        Route::get('transactions/{transaction}/receipt', [TransactionController::class, 'printReceipt'])->name('transactions.receipt');
    });
    Route::middleware('permission:finance.statement')->group(function () {
        Route::get('account-statement', [TransactionController::class, 'accountStatement'])->name('transactions.statement');
        Route::get('account-statement/print', [TransactionController::class, 'printStatement'])->name('transactions.print-statement');
    });
    Route::middleware('permission:finance.categories')->group(function () {
        Route::resource('transaction-categories', TransactionCategoryController::class)->except(['create', 'edit', 'show']);
    });

    // Inventory Management
    Route::middleware('permission:inventory.view')->group(function () {
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('inventory/{product}', [InventoryController::class, 'show'])->name('inventory.show');
    });
    Route::middleware('permission:inventory.barcode')->group(function () {
        Route::get('inventory/{product}/barcode', [InventoryController::class, 'barcodeForm'])->name('inventory.barcode-form');
        Route::get('inventory/barcode/print', [InventoryController::class, 'printBarcode'])->name('inventory.print-barcode');
        Route::post('inventory/barcode/bulk', [InventoryController::class, 'bulkBarcode'])->name('inventory.bulk-barcode');
    });

    // Customers
    Route::middleware('permission:customers.create')->group(function () {
        Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    });
    Route::middleware('permission:customers.view')->group(function () {
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
        Route::get('customers/{customer}/transactions', [CustomerController::class, 'transactions'])->name('customers.transactions');
        Route::get('customers/{customer}/statement', [CustomerController::class, 'accountStatement'])->name('customers.statement');
        Route::get('customers/{customer}/statement/print', [CustomerController::class, 'printStatement'])->name('customers.statement.print');
    });
    Route::middleware('permission:customers.edit')->group(function () {
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::patch('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
    });
    Route::middleware('permission:customers.delete')->group(function () {
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    });
    Route::middleware('permission:customers.transactions')->group(function () {
        Route::get('customers/{customer}/transactions/create', [CustomerController::class, 'addTransaction'])->name('customers.transactions.create');
        Route::post('customers/{customer}/transactions', [CustomerController::class, 'storeTransaction'])->name('customers.transactions.store');
    });

    // POS & Sales
    Route::middleware('permission:sales.pos')->group(function () {
        Route::get('pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('pos/products', [POSController::class, 'getProducts'])->name('pos.products');
        Route::get('pos/products/search', [POSController::class, 'searchProducts'])->name('pos.search');
        Route::post('pos', [POSController::class, 'store'])->name('pos.store');
        Route::post('pos/customer', [POSController::class, 'storeCustomer'])->name('pos.customer.store');
    });
    Route::middleware('permission:sales.view')->group(function () {
        Route::get('pos/history', [POSController::class, 'history'])->name('pos.history');
        Route::get('pos/{sale}', [POSController::class, 'show'])->name('pos.show');
        Route::get('pos/{sale}/receipt', [POSController::class, 'receipt'])->name('pos.receipt');
        Route::get('pos/{sale}/invoice', [POSController::class, 'invoice'])->name('pos.invoice');
    });

    // Sale Returns
    Route::middleware('permission:returns.create')->group(function () {
        Route::get('returns/create', [SaleReturnController::class, 'create'])->name('returns.create');
        Route::post('returns/search-sale', [SaleReturnController::class, 'searchSale'])->name('returns.search-sale');
        Route::post('returns', [SaleReturnController::class, 'store'])->name('returns.store');
    });
    Route::middleware('permission:returns.view')->group(function () {
        Route::get('returns', [SaleReturnController::class, 'index'])->name('returns.index');
        Route::get('returns/{return}', [SaleReturnController::class, 'show'])->name('returns.show');
        Route::get('returns/{return}/receipt', [SaleReturnController::class, 'receipt'])->name('returns.receipt');
    });

    // Coupons
    Route::middleware('permission:coupons.create')->group(function () {
        Route::get('coupons/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
        Route::get('coupons-generate-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
    });
    Route::middleware('permission:coupons.view')->group(function () {
        Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');
        Route::post('coupons/validate', [CouponController::class, 'validate'])->name('coupons.validate');
    });
    Route::middleware('permission:coupons.edit')->group(function () {
        Route::get('coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
        Route::patch('coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    });
    Route::middleware('permission:coupons.delete')->group(function () {
        Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupons.destroy');
    });

    // Reports
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('reports/print', [ReportsController::class, 'print'])->name('reports.print');
        Route::get('reports/export', [ReportsController::class, 'export'])->name('reports.export');
    });
});
