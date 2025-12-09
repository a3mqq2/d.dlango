<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('invoice_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('total_profit', 15, 2);
            $table->enum('payment_method', ['cash', 'credit'])->default('credit');
            $table->foreignId('cashbox_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending_shipment', 'received', 'cancelled'])->default('pending_shipment');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
