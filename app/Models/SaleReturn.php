<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number',
        'sale_id',
        'customer_id',
        'user_id',
        'cashbox_id',
        'return_date',
        'total_amount',
        'refund_method',
        'status',
        'reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Generate unique return number
     */
    public static function generateReturnNumber(): string
    {
        $prefix = 'RET-';
        $date = now()->format('Ymd');
        $lastReturn = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReturn) {
            $lastNumber = (int) substr($lastReturn->return_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . '-' . $newNumber;
    }

    /**
     * Relationships
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashbox(): BelongsTo
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleReturnItem::class);
    }
}
