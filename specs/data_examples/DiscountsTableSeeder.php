<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('discounts')->insert([
            ['id' => 1, 'name' => 'San Valentín', 'description' => 'Descuento san Valentín 20% 2026', 'percentage' => 20, 'start_date' => '2026-02-01 00:00:00', 'end_date' => '2026-02-28 00:00:00', 'status' => 'inactive', 'created_at' => '2026-02-05 19:25:19', 'updated_at' => '2026-04-04 21:43:27'],
        ]);
    }
}