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
        Schema::table('sales', function (Blueprint $table) {
            // Add payment_type column (cash or bank_transfer)
            $table->string('payment_type')->nullable()->after('payment_method')->comment('cash or bank_transfer');
            // Add bank_account column for bank transfer details
            $table->string('bank_account')->nullable()->after('payment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'bank_account']);
        });
    }
};
