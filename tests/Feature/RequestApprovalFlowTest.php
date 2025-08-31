<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestApprovalFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_inventory_request_and_create_reservation()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user  = User::factory()->create(['role' => 'user']);

        $item = Item::factory()->create(['quantity' => 5, 'status' => 'available']);

        $req = ItemRequest::factory()->create([
            'user_id'    => $user->id,
            'type'       => 'inventory',
            'item_id'    => $item->id,
            'quantity'   => 2,
            'start_date' => '2025-03-01',
            'end_date'   => '2025-03-05',
            'status'     => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.requests.approve', $req))
            ->assertRedirect();

        $this->assertDatabaseHas('item_requests', [
            'id' => $req->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('reservations', [
            'item_request_id' => $req->id,
            'item_id'         => $item->id,
            'quantity'        => 2,
            'start_date'      => '2025-03-01 00:00:00',
            'end_date'        => '2025-03-05 00:00:00',
        ]);
    }
}