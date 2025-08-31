<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemRequestFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(['inventory','to-buy']);

        $start = fake()->dateTimeBetween('-20 days', '+10 days');
        $end   = (clone $start)->modify('+'.fake()->numberBetween(1,7).' days');

        return [
            'user_id'    => User::inRandomOrder()->value('id') ?? User::factory(),
            'type'       => $type,
            'item_id'    => $type === 'inventory'
                ? (Item::where('status','available')->inRandomOrder()->value('id') ?? Item::factory())
                : null,
            'quantity'   => fake()->numberBetween(1, 3),
            'start_date' => $type === 'inventory' ? $start->format('Y-m-d') : null,
            'end_date'   => $type === 'inventory' ? $end->format('Y-m-d')   : null,
            'note'       => fake()->optional()->sentence(),
            'status'     => 'pending',
        ];
    }
}