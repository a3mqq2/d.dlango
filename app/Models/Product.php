<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'sku',
        'type',
        'quantity',
        'purchase_price',
        'selling_price',
        'profit_per_unit',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'profit_per_unit' => 'decimal:2',
        ];
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function purchaseInvoiceItems()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function getTotalQuantityAttribute(): int
    {
        if ($this->type === 'simple') {
            return $this->quantity;
        }
        return $this->variants->sum('quantity');
    }

    public static function generateProductCode(): string
    {
        do {
            $code = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get the product image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}
