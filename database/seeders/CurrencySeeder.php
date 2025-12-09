<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\CashRegister;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Currencies
        $dinar = Currency::create([
            'name' => 'دينار ليبي',
            'code' => 'LYD',
            'symbol' => 'د.ل',
            'icon' => 'ti ti-currency',
            'is_default' => true,
            'is_active' => true,
        ]);

        $dollar = Currency::create([
            'name' => 'دولار أمريكي',
            'code' => 'USD',
            'symbol' => '$',
            'icon' => 'ti ti-currency-dollar',
            'is_default' => false,
            'is_active' => true,
        ]);

        // Get admin user for transaction
        $admin = User::where('role', 'admin')->first();

        // Create Cash Registers
        $mainDinarRegister = CashRegister::create([
            'name' => 'الخزينة الرئيسية - دينار',
            'description' => 'الخزينة الرئيسية للدينار الليبي',
            'currency_id' => $dinar->id,
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        $mainDollarRegister = CashRegister::create([
            'name' => 'الخزينة الرئيسية - دولار',
            'description' => 'الخزينة الرئيسية للدولار الأمريكي',
            'currency_id' => $dollar->id,
            'opening_balance' => 0,
            'is_active' => true,
        ]);

        // Create opening balance transactions if admin exists
        if ($admin) {
            Transaction::create([
                'document_number' => 'TRX' . now()->format('Ymd') . '0001',
                'description' => 'رصيد افتتاحي - الخزينة الرئيسية دينار',
                'amount' => 0,
                'type' => 'opening_balance',
                'cash_register_id' => $mainDinarRegister->id,
                'user_id' => $admin->id,
                'transaction_date' => now()->toDateString(),
            ]);

            Transaction::create([
                'document_number' => 'TRX' . now()->format('Ymd') . '0002',
                'description' => 'رصيد افتتاحي - الخزينة الرئيسية دولار',
                'amount' => 0,
                'type' => 'opening_balance',
                'cash_register_id' => $mainDollarRegister->id,
                'user_id' => $admin->id,
                'transaction_date' => now()->toDateString(),
            ]);
        }
    }
}
