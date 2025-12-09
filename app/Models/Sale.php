<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'cashbox_id',
        'sale_date',
        'subtotal',
        'discount',
        'discount_type',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'status',
        'notes',
        'coupon_id',
        'coupon_discount',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'coupon_discount' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'SL';
        $date = date('Ymd');
        $lastInvoice = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    /**
     * Scope for completed sales
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for today's sales
     */
    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    /**
     * Get items count
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
