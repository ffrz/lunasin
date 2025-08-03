<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Bisnis', 'user_id' => 1],
            ['name' => 'Pribadi', 'user_id' => 1],
            ['name' => 'Lainnya', 'user_id' => 1],
        ];

        DB::table('transaction_categories')->insert($items);
    }
}
