<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusHistoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('order_status_histories')->insert([
            ['id' => 1, 'order_id' => 15, 'from_status' => 'pending', 'to_status' => 'shipping', 'changed_by' => 1, 'notes' => null, 'changed_at' => '2026-04-07 20:09:08', 'created_at' => '2026-04-07 20:09:08', 'updated_at' => '2026-04-07 20:09:08'],
            ['id' => 2, 'order_id' => 15, 'from_status' => 'shipping', 'to_status' => 'shipped', 'changed_by' => 1, 'notes' => null, 'changed_at' => '2026-04-07 20:09:45', 'created_at' => '2026-04-07 20:09:45', 'updated_at' => '2026-04-07 20:09:45'],
            ['id' => 4, 'order_id' => 14, 'from_status' => 'pending', 'to_status' => 'canceled', 'changed_by' => 1, 'notes' => null, 'changed_at' => '2026-04-07 20:41:20', 'created_at' => '2026-04-07 20:41:20', 'updated_at' => '2026-04-07 20:41:20'],
            ['id' => 5, 'order_id' => 16, 'from_status' => 'pending', 'to_status' => 'shipping', 'changed_by' => 1, 'notes' => null, 'changed_at' => '2026-04-07 20:43:12', 'created_at' => '2026-04-07 20:43:12', 'updated_at' => '2026-04-07 20:43:12'],
            ['id' => 6, 'order_id' => 17, 'from_status' => 'pending', 'to_status' => 'canceled', 'changed_by' => 1, 'notes' => null, 'changed_at' => '2026-04-07 21:37:39', 'created_at' => '2026-04-07 21:37:39', 'updated_at' => '2026-04-07 21:37:39'],
            ['id' => 7, 'order_id' => 34, 'from_status' => 'pending', 'to_status' => 'shipping', 'changed_by' => 1, 'notes' => null, 'changed_at' => '2026-04-08 15:50:13', 'created_at' => '2026-04-08 15:50:13', 'updated_at' => '2026-04-08 15:50:13'],
        ]);
    }
}