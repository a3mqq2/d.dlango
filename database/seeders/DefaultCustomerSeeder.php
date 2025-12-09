<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class DefaultCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default walk-in customer if it doesn't exist
        if (!Customer::where('is_default', true)->exists()) {
            Customer::create([
                'name' => 'زبون نقدي',
                'phone' => null,
                'balance' => 0,
                'is_default' => true,
                'is_active' => true,
            ]);
        }
    }
}
