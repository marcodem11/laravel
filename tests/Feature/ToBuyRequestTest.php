<?php

namespace Tests\Feature;

use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToBuyRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_to_buy_request_and_it_is_listed_in_mine()
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('requests.store'), [
                'type'       => 'to-buy',
                'note'       => 'Serve una sedia ergonomica',
                'quantity'   => 1,
                // niente item/date
            ])->assertRedirect(route('requests.mine'));

        $this->assertDatabaseHas('item_requests', [
            'user_id'  => $user->id,
            'type'     => 'to-buy',
            'status'   => 'pending',
            'note'     => 'Serve una sedia ergonomica',
        ]);

        // La pagina "Le mie richieste" renderizza la richiesta
        $this->actingAs($user)->get(route('requests.mine'))
            ->assertOk()
            ->assertSee('to-buy')
            ->assertSee('Serve una sedia ergonomica');
    }
}