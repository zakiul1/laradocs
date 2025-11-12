<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipper;

class ShipperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shippers = [
            [
                'name' => 'Global Logistics Ltd',
                'address' => 'Dhaka Export Processing Zone, Dhaka',
                'email' => 'info@globallogistics.com',
                'phone' => '+8801712345001',
                'website' => 'https://globallogistics.com',
                'created_by' => 1,
            ],
            [
                'name' => 'FastShip International',
                'address' => 'Chittagong Port Area, Chittagong',
                'email' => 'contact@fastship.com',
                'phone' => '+8801712345002',
                'website' => 'https://fastship.com',
                'created_by' => 1,
            ],
            [
                'name' => 'TransWorld Shippers',
                'address' => 'Khulna Industrial Zone, Khulna',
                'email' => 'support@transworld.com',
                'phone' => '+8801712345003',
                'website' => 'https://transworld.com',
                'created_by' => 1,
            ],
        ];

        foreach ($shippers as $shipper) {
            Shipper::create($shipper);
        }
    }
}
