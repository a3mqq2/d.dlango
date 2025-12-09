<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashbox extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'current_balance',
        'opening_balance',
    ];

    protected function casts(): array
    {
        return [
            'current_balance' => 'decimal:2',
            'opening_balance' => 'decimal:2',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getFormattedCurrentBalanceAttribute(): string
    {
        return number_format($this->current_balance, 2);
    }

    public function getFormattedOpeningBalanceAttribute(): string
    {
        return number_format($this->opening_balance, 2);
    }
}
