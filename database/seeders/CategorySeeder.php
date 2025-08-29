<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Laptops',
            'Monitors',
            'Peripherals',
            'Accessories',
            'Phones & Tablets',
            'Audio/Video',
            'Networking',
        ];

        foreach ($names as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}