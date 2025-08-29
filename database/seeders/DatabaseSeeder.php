<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chiama il nostro DemoSeeder
        $this->call([
            DemoSeeder::class,
            CategorySeeder::class,
        ]);
    }
}