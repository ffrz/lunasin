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
                'name' => 'Admin',
                'role' => User::Role_Admin,
            ],
            [
                'id' => 2,
                'name' => 'User',
                'role' => User::Role_User,
            ],
        ];

        $password = Hash::make('12345');

        foreach ($users as &$user) {
            $username = strtolower(str_replace(' ', '', $user['name']));
            $user['name'] = ucwords(strtolower($user['name']));
            $user['username'] = $username;
            $user['password'] = $password;
            $user['active'] = true;
        }

        DB::table('users')->insert($users);
    }
}
