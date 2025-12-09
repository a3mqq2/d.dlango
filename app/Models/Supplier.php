<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    /**
     * Get all transactions for the supplier
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get purchase invoices for the supplier
     */
    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    /**
     * Get balance formatted
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2);
    }

    /**
     * Get total deposits (payments we received from supplier)
     */
    public function getTotalDepositsAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'deposit')->sum('amount');
    }

    /**
     * Get total withdrawals (payments we made to supplier)
     */
    public function getTotalWithdrawalsAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'withdrawal')->sum('amount');
    }

    /**
     * Recalculate and update balance based on transactions
     */
    public function recalculateBalance(): void
    {
        // Balance = deposits - withdrawals
        // Positive = supplier owes us (credit)
        // Negative = we owe supplier (debit)
        $this->balance = $this->total_deposits - $this->total_withdrawals;
        $this->save();
    }
}
