<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemsCreateCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_item_via_artisan_command()
    {
        $cat = Category::factory()->create(['name' => 'Laptops']);

        $this->artisan('items:create', [
            '--name'        => 'Test UltraBook',
            '--category'    => (string)$cat->id,
            '--quantity'    => '7',
            '--status'      => 'available',
            '--description' => 'Notebook leggero per demo',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('items', [
            'name'        => 'Test UltraBook',
            'category_id' => $cat->id,
            'quantity'    => 7,
            'status'      => 'available',
        ]);
    }
}