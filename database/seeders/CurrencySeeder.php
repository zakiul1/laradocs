<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'USD','name' => 'US Dollar','symbol' => '$','active' => true],
            ['code' => 'EUR','name' => 'Euro','symbol' => '€','active' => true],
            ['code' => 'BDT','name' => 'Bangladeshi Taka','symbol' => '৳','active' => true],
        ];

        foreach ($rows as $r) {
            DB::table('currencies')->updateOrInsert(['code' => $r['code']], $r + ['created_at'=>now(),'updated_at'=>now()]);
        }
    }
}
