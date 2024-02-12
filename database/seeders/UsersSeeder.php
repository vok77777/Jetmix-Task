<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            'name' => 'Manager',
            'email' => 'manager@mail.ru',
            'password' => Hash::make('123123123'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('users')->insertOrIgnore($data);

        $manager = DB::table('users')->where('email', 'manager@mail.ru')->first();
        $managerRole = DB::table('roles')->where('slug', 'manager')->first();

        DB::table('role_users')->insertOrIgnore([
            'user_id' => $manager->id,
            'role_id' => $managerRole->id
        ]);
    }
}
