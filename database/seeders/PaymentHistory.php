<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentHistory as PaymentHistoryModel;

class PaymentHistory extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentHistoryModel::factory()->count(50)->create();
    }
}
