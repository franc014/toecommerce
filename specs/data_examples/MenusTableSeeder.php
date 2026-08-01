<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menus')->insert([
            ['id' => 1, 'title' => 'Main', 'slug' => 'main', 'created_at' => '2025-11-25 20:00:47', 'updated_at' => '2025-11-25 20:00:47'],
            ['id' => 2, 'title' => 'Footer', 'slug' => 'footer', 'created_at' => '2025-11-25 20:05:02', 'updated_at' => '2025-11-25 20:05:02'],
            ['id' => 3, 'title' => 'Legal', 'slug' => 'legal', 'created_at' => '2025-11-25 20:05:09', 'updated_at' => '2025-11-25 20:05:09'],
        ]);
    }
}