<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed admin
        User::firstOrCreate(
            ['email' => 'admin@company.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        // Fixed demo users
        User::firstOrCreate(
            ['email' => 'john.doe@company.com'],
            ['name' => 'John Doe', 'password' => Hash::make('password'), 'role' => 'user']
        );

        User::firstOrCreate(
            ['email' => 'jane.smith@company.com'],
            ['name' => 'Jane Smith', 'password' => Hash::make('password'), 'role' => 'user']
        );

        // Other random users
        User::factory(6)->create(['role' => 'user']);

        // Categories
        $categories = ['Laptops', 'Monitors', 'Smartphones', 'Peripherals', 'Accessories'];
        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        // Items with realistic names
        $items = [
            ['category' => 'Laptops', 'name' => 'MacBook Pro 16"', 'quantity' => 5],
            ['category' => 'Laptops', 'name' => 'Dell XPS 13', 'quantity' => 8],
            ['category' => 'Monitors', 'name' => 'LG UltraWide 34"', 'quantity' => 6],
            ['category' => 'Monitors', 'name' => 'Dell 27" 4K', 'quantity' => 4],
            ['category' => 'Smartphones', 'name' => 'iPhone 16', 'quantity' => 10],
            ['category' => 'Smartphones', 'name' => 'Samsung Galaxy S24', 'quantity' => 7],
            ['category' => 'Peripherals', 'name' => 'Logitech MX Keys', 'quantity' => 12],
            ['category' => 'Peripherals', 'name' => 'Logitech MX Master 3S', 'quantity' => 9],
            ['category' => 'Accessories', 'name' => 'USB-C Hub', 'quantity' => 15],
            ['category' => 'Accessories', 'name' => 'Laptop Backpack', 'quantity' => 10],
        ];

        foreach ($items as $data) {
            $cat = Category::where('name', $data['category'])->first();
            Item::firstOrCreate(
                ['name' => $data['name']],
                [
                    'category_id' => $cat->id,
                    'quantity' => $data['quantity'],
                    'status' => 'available',
                    'description' => fake()->sentence(8),
                ]
            );
        }

        // Extra random items
        Item::factory(10)->create(['status' => 'available']);
        Item::factory(3)->create(['status' => 'unavailable']);

        // Requests
        $requests = ItemRequest::factory(30)->create();

        // Approve ~60% with reservations
        $availability = app(AvailabilityService::class);

        $requests->each(function (ItemRequest $req) use ($availability) {
            if ($req->type !== 'inventory') {
                $req->update(['status' => fake()->boolean(60) ? 'approved' : 'rejected']);
                return;
            }

            $item = Item::find($req->item_id);
            if (!$item) { 
                $req->update(['status'=>'rejected']); 
                return; 
            }

            $available = $availability->availableQuantity($item, $req->start_date, $req->end_date);
            if ($available >= $req->quantity) {
                Reservation::create([
                    'item_request_id' => $req->id,
                    'item_id'         => $item->id,
                    'start_date'      => $req->start_date,
                    'end_date'        => $req->end_date,
                    'quantity'        => $req->quantity,
                ]);
                $req->update(['status'=>'approved']);
            } else {
                $req->update(['status'=>'rejected']);
            }
        });
    }
}