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
        // Drop foreign key and column from transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['transaction_category_id']);
            $table->dropColumn('transaction_category_id');
        });

        // Drop the transaction_categories table
        Schema::dropIfExists('transaction_categories');

        // Delete finance.categories permission
        \App\Models\Permission::where('name', 'finance.categories')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate transaction_categories table
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // Add back the column to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('transaction_category_id')->nullable()->constrained('transaction_categories')->onDelete('set null');
        });
    }
};
