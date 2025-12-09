<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'discount',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function returnItems()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    /**
     * Get returned quantity for this item
     */
    public function getReturnedQuantityAttribute(): int
    {
        return $this->returnItems()->whereHas('saleReturn', function ($q) {
            $q->where('status', 'completed');
        })->sum('quantity');
    }

    /**
     * Get remaining quantity that can be returned
     */
    public function getReturnableQuantityAttribute(): int
    {
        return $this->quantity - $this->returned_quantity;
    }

    /**
     * Get product display name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->variant) {
            return $this->product->name . ' - ' . $this->variant->variant_name;
        }
        return $this->product->name;
    }
}
