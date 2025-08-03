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
            ['name' => 'Bisnis'],
            ['name' => 'Pribadi'],
            ['name' => 'Lainnya'],
        ];

        DB::table('transaction_categories')->insert($items);
    }
}
