<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        // pool nomi realistici per categoria
        $pools = [
            'Laptops' => [
                'MacBook Pro 14"', 'MacBook Pro 16"', 'Dell XPS 13', 'HP EliteBook',
                'Lenovo ThinkPad X1', 'Acer Swift 5',
            ],
            'Monitors' => [
                'Dell 27" 4K', 'LG UltraWide 34"', 'Samsung 32" Curved', 'BenQ PD2700',
            ],
            'Smartphones' => [
                'iPhone 16', 'iPhone 16 Pro', 'Samsung Galaxy S24', 'Google Pixel 9',
            ],
            'Peripherals' => [
                'Logitech MX Keys', 'Logitech MX Master 3S', 'Anker USB-C Dock', 'Apple Magic Mouse',
            ],
            'Accessories' => [
                'USB-C Hub', 'Laptop Backpack', 'HDMI Cable 2m', 'USB-C to Lightning Cable',
            ],
        ];

        // scegli o crea una categoria
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();

        // scegli nome dal pool (se la categoria non è nel pool, usa un fallback sensato)
        $pool = $pools[$category->name] ?? [
            'Generic Device', 'Office Accessory', 'Company Equipment', 'IT Asset',
        ];
        $name = fake()->unique()->randomElement($pool);

        return [
            'category_id' => $category->id,
            'name'        => $name,
            'quantity'    => fake()->numberBetween(1, 10),
            'status'      => fake()->randomElement(['available', 'unavailable']),
            'description' => fake()->sentence(8),
        ];
    }
}