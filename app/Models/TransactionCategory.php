<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    /**
     * Scope for user categories (non-system)
     */
    public function scopeUserCategories($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope for system categories
     */
    public function scopeSystemCategories($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Create or get a system category
     */
    public static function getSystemCategory(string $name): self
    {
        return static::firstOrCreate(
            ['name' => $name],
            ['is_system' => true]
        );
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTransactionCountAttribute(): int
    {
        return $this->transactions()->count();
    }
}
