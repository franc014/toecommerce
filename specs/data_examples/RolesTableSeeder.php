<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'super_admin',
                'guard_name' => 'web',
                'created_at' => '2025-11-23 00:41:49',
                'updated_at' => '2025-11-23 00:41:49',
            ],
            [
                'id' => 2,
                'name' => 'customer',
                'guard_name' => 'web',
                'created_at' => '2025-11-23 00:41:50',
                'updated_at' => '2025-11-23 00:41:50',
            ],
        ]);
    }
}