<?php

namespace Database\Seeders;

use App\Models\Party;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'id' => 1,
                'user_id' => 1,
                'name' => 'Ayat HTC',
                'type' => Party::Type_Company,
                'active' => true,
                'balance' => 0,
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'name' => 'Anadzsa',
                'role' => Party::Type_Company,
                'active' => true,
                'balance' => 0,
            ],
            [
                'id' => 3,
                'user_id' => 1,
                'name' => 'Ajis',
                'role' => Party::Type_Personal,
                'active' => true,
                'balance' => 0,
            ],
            [
                'id' => 5,
                'user_id' => 1,
                'name' => 'Eman',
                'role' => Party::Type_Personal,
                'active' => true,
                'balance' => 0,
            ],
        ];

        DB::table('parties')->insert($items);
    }
}
