<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class GlobalCategorySeeder extends Seeder
{
    public function run(): void
    {
        // FACTORY defaults
        $knit = Category::firstOrCreate(['scope' => 'factory', 'name' => 'Knit Factory', 'parent_id' => null]);
        foreach (['Cutting', 'Stitching', 'Finishing'] as $n) {
            Category::firstOrCreate(['scope' => 'factory', 'name' => $n, 'parent_id' => $knit->id]);
        }
        $woven = Category::firstOrCreate(['scope' => 'factory', 'name' => 'Woven Factory', 'parent_id' => null]);
        foreach (['Weaving', 'Dyeing', 'Finishing'] as $n) {
            Category::firstOrCreate(['scope' => 'factory', 'name' => $n, 'parent_id' => $woven->id]);
        }

        // EMPLOYEE example (optional)
        $dept = Category::firstOrCreate(['scope' => 'employee', 'name' => 'Department', 'parent_id' => null]);
        foreach (['HR', 'IT', 'Accounts'] as $n) {
            Category::firstOrCreate(['scope' => 'employee', 'name' => $n, 'parent_id' => $dept->id]);
        }
    }
}