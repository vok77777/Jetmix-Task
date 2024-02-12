<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            [
                'slug' => 'client',
                'name' => 'Client',
            ],
            [
                'slug' => 'manager',
                'name' => 'Manager',
            ]
        ];

        foreach($data as &$value) {
            $value['created_at'] = now();
            $value['updated_at'] = now();
        }

        DB::table('roles')->insertOrIgnore($data);
    }
}
