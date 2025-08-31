<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_full_quantity_when_no_reservations_overlap()
    {
        $item = Item::factory()->create(['quantity' => 5]);

        Reservation::factory()->create([
            'item_id'     => $item->id,
            'start_date'  => '2025-01-01',
            'end_date'    => '2025-01-03',
            'quantity'    => 2,
        ]);

        $svc = app(AvailabilityService::class);
        $avail = $svc->availableQuantity($item, '2025-02-01', '2025-02-05');

        $this->assertSame(5, $avail);
    }

    public function test_subtracts_overlapping_reservations_from_stock()
    {
        $item = Item::factory()->create(['quantity' => 5]);

        Reservation::factory()->create([
            'item_id'     => $item->id,
            'start_date'  => '2025-02-02',
            'end_date'    => '2025-02-04',
            'quantity'    => 3,
        ]);

        $svc = app(AvailabilityService::class);
        $avail = $svc->availableQuantity($item, '2025-02-01', '2025-02-05');

        $this->assertSame(2, $avail);
    }

    public function test_never_returns_negative_values()
    {
        $item = Item::factory()->create(['quantity' => 2]);

        Reservation::factory()->create([
            'item_id'     => $item->id,
            'start_date'  => '2025-02-01',
            'end_date'    => '2025-02-10',
            'quantity'    => 5,
        ]);

        $svc = app(AvailabilityService::class);
        $avail = $svc->availableQuantity($item, '2025-02-03', '2025-02-04');

        $this->assertSame(0, $avail);
    }
}