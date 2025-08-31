<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects(): void
    {
        $this->get('/')->assertStatus(302); // oppure ->assertRedirect()
    }
}