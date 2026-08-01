<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTaxTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_tax')->insert([
            ['id' => 17, 'product_id' => 1, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 18, 'product_id' => 2, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 19, 'product_id' => 3, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 20, 'product_id' => 4, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 21, 'product_id' => 5, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 22, 'product_id' => 6, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 23, 'product_id' => 7, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 24, 'product_id' => 8, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 25, 'product_id' => 9, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 26, 'product_id' => 10, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 27, 'product_id' => 11, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 28, 'product_id' => 12, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 29, 'product_id' => 13, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 30, 'product_id' => 14, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 31, 'product_id' => 15, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
            ['id' => 32, 'product_id' => 16, 'tax_id' => 1, 'created_at' => null, 'updated_at' => null],
        ]);
    }
}