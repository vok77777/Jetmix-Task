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
            'email' => env('MANAGER_EMAIL'),
            'password' => Hash::make(env('MANAGER_PASSWORD')),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('users')->insertOrIgnore($data);

        $manager = DB::table('users')->where('email', env('MANAGER_EMAIL'))->first();
        $managerRole = DB::table('roles')->where('slug', 'manager')->first();

        DB::table('role_users')->insertOrIgnore([
            'user_id' => $manager->id,
            'role_id' => $managerRole->id
        ]);
    }
}
