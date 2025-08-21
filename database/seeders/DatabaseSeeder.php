<?php

/**
 * MIT License
 * 
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * See LICENSE file in the project root for full license information.
 * 
 * GitHub: https://github.com/ffrz
 * Email: fahmifauzirahman@gmail.com
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->call([
                UserSeeder::class,
                PartySeeder::class,
                TransactionCategorySeeder::class,
            ]);
        });
    }
}
