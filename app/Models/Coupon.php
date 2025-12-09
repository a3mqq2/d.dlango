<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'usage_limit_per_customer',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'usage_limit_per_customer' => 'integer',
            'used_count' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get coupon usage records
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get sales that used this coupon
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for valid (usable) coupons
     */
    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                  ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->start_date && $this->start_date > now()) {
            return false;
        }

        if ($this->end_date && $this->end_date < now()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon can be used by customer
     */
    public function canBeUsedByCustomer(?int $customerId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($customerId && $this->usage_limit_per_customer) {
            $customerUsageCount = $this->usages()
                ->where('customer_id', $customerId)
                ->count();

            if ($customerUsageCount >= $this->usage_limit_per_customer) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate discount for order
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->min_order_amount && $orderTotal < $this->min_order_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = ($orderTotal * $this->value) / 100;

            // Apply max discount limit if set
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }

            return $discount;
        }

        // Fixed discount
        return min($this->value, $orderTotal);
    }

    /**
     * Get validation message
     */
    public function getValidationMessage(?int $customerId = null, ?float $orderTotal = null): ?string
    {
        if (!$this->is_active) {
            return __('messages.coupon_inactive');
        }

        if ($this->start_date && $this->start_date > now()) {
            return __('messages.coupon_not_started');
        }

        if ($this->end_date && $this->end_date < now()) {
            return __('messages.coupon_expired');
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return __('messages.coupon_usage_limit_reached');
        }

        if ($customerId && $this->usage_limit_per_customer) {
            $customerUsageCount = $this->usages()
                ->where('customer_id', $customerId)
                ->count();

            if ($customerUsageCount >= $this->usage_limit_per_customer) {
                return __('messages.coupon_customer_limit_reached');
            }
        }

        if ($orderTotal !== null && $this->min_order_amount && $orderTotal < $this->min_order_amount) {
            return __('messages.coupon_min_order_not_met', [
                'amount' => number_format($this->min_order_amount, 2)
            ]);
        }

        return null;
    }

    /**
     * Record usage
     */
    public function recordUsage(int $customerId, int $saleId, float $discountAmount): void
    {
        CouponUsage::create([
            'coupon_id' => $this->id,
            'customer_id' => $customerId,
            'sale_id' => $saleId,
            'discount_amount' => $discountAmount,
        ]);

        $this->increment('used_count');
    }

    /**
     * Get discount display text
     */
    public function getDiscountTextAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }

        return number_format($this->value, 2) . ' ' . __('messages.currency');
    }

    /**
     * Get status badge
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->start_date && $this->start_date > now()) {
            return 'scheduled';
        }

        if ($this->end_date && $this->end_date < now()) {
            return 'expired';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'exhausted';
        }

        return 'active';
    }

    /**
     * Generate unique coupon code
     */
    public static function generateCode(int $length = 8): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        do {
            $code = '';
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
