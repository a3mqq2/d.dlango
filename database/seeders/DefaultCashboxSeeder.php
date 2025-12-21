<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cashbox;

class DefaultCashboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default main cashbox if it doesn't exist
        Cashbox::firstOrCreate(
            ['name' => 'الخزينة الرئيسية'],
            [
                'current_balance' => 0,
                'opening_balance' => 0,
            ]
        );
    }
}
