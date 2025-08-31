<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $item = Item::inRandomOrder()->first() ?? Item::factory()->create();

        $start = fake()->dateTimeBetween('-10 days', '+20 days');
        $end   = (clone $start)->modify('+'.fake()->numberBetween(1,5).' days');

        return [
            'item_request_id' => ItemRequest::factory(),
            'item_id'         => $item->id,
            'start_date'      => $start->format('Y-m-d'),
            'end_date'        => $end->format('Y-m-d'),
            'quantity'        => fake()->numberBetween(1, 2),
        ];
    }
}