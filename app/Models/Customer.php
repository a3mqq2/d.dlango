<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'balance',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the default walk-in customer
     */
    public static function getDefaultCustomer(): self
    {
        return static::where('is_default', true)->first()
            ?? static::create([
                'name' => __('messages.walk_in_customer'),
                'phone' => null,
                'balance' => 0,
                'is_default' => true,
                'is_active' => true,
            ]);
    }

    /**
     * Scope for active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for non-default customers
     */
    public function scopeRegular($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Get all transactions for the customer
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all sales for the customer
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get balance formatted
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2);
    }

    /**
     * Get total deposits (payments received from customer)
     */
    public function getTotalDepositsAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'deposit')->sum('amount');
    }

    /**
     * Get total withdrawals (refunds/credits given to customer)
     */
    public function getTotalWithdrawalsAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'withdrawal')->sum('amount');
    }

    /**
     * Recalculate and update balance based on transactions
     * Positive balance = customer owes us
     * Negative balance = we owe customer (credit)
     */
    public function recalculateBalance(): void
    {
        // For customers: withdrawals increase their debt (we give them goods on credit)
        // deposits decrease their debt (they pay us)
        $this->balance = $this->total_withdrawals - $this->total_deposits;
        $this->save();
    }
}
