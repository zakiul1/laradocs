<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Seed a few root categories and their children for scope "factory".
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Define your tree (roots + children). Add/edit as you like.
            $tree = [
                [
                    'name' => 'Factory Type',
                    'children' => ['Woven', 'Knit', 'Sweater', 'Denim'],
                ],
                [
                    'name' => 'Compliance',
                    'children' => ['BSCI', 'WRAP', 'SEDEX', 'ISO 9001', 'OEKO-TEX'],
                ],
                [
                    'name' => 'Capabilities',
                    'children' => ['Outerwear', 'Underwear', 'Activewear', 'Kidswear'],
                ],
                [
                    'name' => 'Certificates',
                    'children' => ['GOTS', 'BCI', 'OCS', 'GRS'],
                ],
            ];

            $scope = 'factory';
            $rootPosition = 1;

            foreach ($tree as $node) {
                // Create or fetch root
                /** @var \App\Models\Category $root */
                $root = Category::firstOrCreate(
                    ['name' => $node['name'], 'scope' => $scope, 'parent_id' => null],
                    ['position' => $rootPosition]
                );

                // Create or fetch children
                $childPosition = 1;
                foreach ($node['children'] as $childName) {
                    Category::firstOrCreate(
                        ['name' => $childName, 'scope' => $scope, 'parent_id' => $root->id],
                        ['position' => $childPosition]
                    );
                    $childPosition++;
                }

                $rootPosition++;
            }
        });
    }
}