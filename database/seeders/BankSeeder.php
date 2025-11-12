<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'name' => 'Eastern Bank Ltd',
                'type' => 'Customer Bank',
                'email' => 'info@ebl.com',
                'address' => 'Dhaka, Bangladesh',
                'phone' => '+8801712345678',
                'country' => 'Bangladesh',
                'note' => 'Main customer transaction bank',
                'created_by' => 1,
            ],
            [
                'name' => 'City Bank',
                'type' => 'Customer Bank',
                'email' => 'support@citybank.com',
                'address' => 'Chittagong, Bangladesh',
                'phone' => '+8801887654321',
                'country' => 'Bangladesh',
                'note' => 'Secondary customer bank',
                'created_by' => 1,
            ],
            [
                'name' => 'Standard Chartered',
                'type' => 'Shipper Bank',
                'email' => 'contact@sc.com',
                'address' => 'London, UK',
                'phone' => '+442078123456',
                'country' => 'United Kingdom',
                'note' => 'Used for international shipping transactions',
                'created_by' => 1,
            ],
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
