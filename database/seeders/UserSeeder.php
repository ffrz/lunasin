<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Fahmi Fauzi Rahman',
                'email' => 'fahmi@example.com',
            ],
            [
                'id' => 2,
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ],
        ];

        $password = Hash::make('123456');

        foreach ($users as &$user) {
            $user['password'] = $password;
            $user['active'] = true;
        }

        DB::table('users')->insert($users);
    }
}
