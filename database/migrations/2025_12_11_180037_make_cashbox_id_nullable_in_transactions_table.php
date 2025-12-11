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
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['cashbox_id']);

            // Make cashbox_id nullable
            $table->foreignId('cashbox_id')->nullable()->change()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['cashbox_id']);

            // Make cashbox_id not nullable again
            $table->foreignId('cashbox_id')->nullable(false)->change()->constrained()->onDelete('cascade');
        });
    }
};
