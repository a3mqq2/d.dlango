<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('value', 15, 2);
            $table->decimal('min_order_amount', 15, 2)->nullable();
            $table->decimal('max_discount', 15, 2)->nullable(); // For percentage coupons
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_limit_per_customer')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for tracking coupon usage per customer
        Schema::create('coupon_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->decimal('discount_amount', 15, 2);
            $table->timestamps();
        });

        // Add coupon_id to sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('notes')->constrained()->nullOnDelete();
            $table->decimal('coupon_discount', 15, 2)->default(0)->after('coupon_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'coupon_discount']);
        });

        Schema::dropIfExists('coupon_usage');
        Schema::dropIfExists('coupons');
    }
};
