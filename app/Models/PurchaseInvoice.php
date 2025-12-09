<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'invoice_date',
        'total_amount',
        'total_profit',
        'payment_method',
        'cashbox_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'total_amount' => 'decimal:2',
            'total_profit' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending_shipment' => 'warning',
            'received' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public static function generateInvoiceNumber(): string
    {
        $lastInvoice = self::latest('id')->first();
        $nextNumber = $lastInvoice ? (intval(substr($lastInvoice->invoice_number, 3)) + 1) : 1;
        return 'INV' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
