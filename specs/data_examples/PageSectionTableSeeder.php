<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageSectionTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('page_section')->insert([
            ['section_id' => 3, 'page_id' => 1, 'order_column' => 1, 'created_at' => null, 'updated_at' => null],
            ['section_id' => 1, 'page_id' => 1, 'order_column' => 2, 'created_at' => null, 'updated_at' => null],
            ['section_id' => 4, 'page_id' => 1, 'order_column' => 3, 'created_at' => null, 'updated_at' => null],
            ['section_id' => 2, 'page_id' => 1, 'order_column' => 4, 'created_at' => null, 'updated_at' => null],
            ['section_id' => 5, 'page_id' => 2, 'order_column' => 5, 'created_at' => '2025-12-18 23:48:14', 'updated_at' => '2025-12-18 23:48:14'],
            ['section_id' => 6, 'page_id' => 2, 'order_column' => 6, 'created_at' => '2025-12-18 23:48:14', 'updated_at' => '2025-12-18 23:48:14'],
            ['section_id' => 7, 'page_id' => 2, 'order_column' => 7, 'created_at' => '2025-12-18 23:48:14', 'updated_at' => '2025-12-18 23:48:14'],
            ['section_id' => 9, 'page_id' => 3, 'order_column' => 8, 'created_at' => '2025-12-18 23:48:54', 'updated_at' => '2025-12-18 23:48:54'],
            ['section_id' => 8, 'page_id' => 4, 'order_column' => 9, 'created_at' => '2025-12-18 23:49:25', 'updated_at' => '2025-12-18 23:49:25'],
            ['section_id' => 10, 'page_id' => 5, 'order_column' => 10, 'created_at' => '2025-12-31 00:09:02', 'updated_at' => '2025-12-31 00:09:02'],
            ['section_id' => 11, 'page_id' => 6, 'order_column' => 11, 'created_at' => '2025-12-31 00:10:22', 'updated_at' => '2025-12-31 00:10:22'],
            ['section_id' => 12, 'page_id' => 7, 'order_column' => 12, 'created_at' => '2025-12-31 00:11:23', 'updated_at' => '2025-12-31 00:11:23'],
        ]);
    }
}