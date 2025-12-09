<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'variant_name',
        'quantity',
        'purchase_price',
        'selling_price',
        'profit_per_unit',
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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchaseInvoiceItems()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public static function generateVariantCode(): string
    {
        do {
            $code = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
