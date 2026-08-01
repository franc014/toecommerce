<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('taxes')->insert([
            ['id' => 1, 'name' => 'IVA', 'description' => 'Impuesto al valor agregado', 'percentage' => 15, 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
        ]);
    }
}