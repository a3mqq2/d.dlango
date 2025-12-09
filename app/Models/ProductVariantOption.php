<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'attribute_id',
        'attribute_option_id',
    ];

    /**
     * Get the variant
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the attribute
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the attribute option
     */
    public function attributeOption()
    {
        return $this->belongsTo(AttributeOption::class);
    }
}
