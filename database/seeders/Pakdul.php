<?php

namespace Database\Seeders;

use App\Models\PakDul\PayLatterConfig;
use Illuminate\Database\Seeder;

class Pakdul extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PayLatterConfig::create([
            'toko_id' => 1,
            'default_limit' => 100000,
            'max_limit' => 500000,
            'max_loan_days' => 30,
            'grace_period_days' => 7,
            'interest_rate' => 0.05,
            'penalty_rate' => 0.1,
            'is_active' => true,
        ]);
    }
}
