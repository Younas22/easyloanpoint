<?php

namespace Database\Seeders;

use App\Models\LoanType;
use Illuminate\Database\Seeder;

class LoanTypeSeeder extends Seeder
{
    public function run(): void
    {
        LoanType::firstOrCreate(
            ['name' => 'Easy Loan'],
            [
                'description'    => 'Quick and easy loan for immediate financial needs.',
                'amount'         => 3000.00,
                'repayment_days' => 6,
                'status'         => 'active',
                'sort_order'     => 0,
            ]
        );
    }
}
