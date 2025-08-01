<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Party::factory(5)->create();
    }
}
