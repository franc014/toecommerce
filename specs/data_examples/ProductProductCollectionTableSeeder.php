<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductProductCollectionTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_product_collection')->insert([
            ['id' => 39, 'product_id' => 1, 'product_collection_id' => 11, 'created_at' => null, 'updated_at' => null],
            ['id' => 40, 'product_id' => 2, 'product_collection_id' => 11, 'created_at' => null, 'updated_at' => null],
            ['id' => 41, 'product_id' => 3, 'product_collection_id' => 11, 'created_at' => null, 'updated_at' => null],
            ['id' => 42, 'product_id' => 4, 'product_collection_id' => 11, 'created_at' => null, 'updated_at' => null],
            ['id' => 43, 'product_id' => 5, 'product_collection_id' => 12, 'created_at' => null, 'updated_at' => null],
            ['id' => 44, 'product_id' => 6, 'product_collection_id' => 12, 'created_at' => null, 'updated_at' => null],
            ['id' => 45, 'product_id' => 7, 'product_collection_id' => 12, 'created_at' => null, 'updated_at' => null],
            ['id' => 46, 'product_id' => 8, 'product_collection_id' => 13, 'created_at' => null, 'updated_at' => null],
            ['id' => 47, 'product_id' => 9, 'product_collection_id' => 13, 'created_at' => null, 'updated_at' => null],
            ['id' => 48, 'product_id' => 10, 'product_collection_id' => 14, 'created_at' => null, 'updated_at' => null],
            ['id' => 49, 'product_id' => 11, 'product_collection_id' => 14, 'created_at' => null, 'updated_at' => null],
            ['id' => 50, 'product_id' => 12, 'product_collection_id' => 14, 'created_at' => null, 'updated_at' => null],
            ['id' => 51, 'product_id' => 13, 'product_collection_id' => 14, 'created_at' => null, 'updated_at' => null],
            ['id' => 52, 'product_id' => 14, 'product_collection_id' => 13, 'created_at' => null, 'updated_at' => null],
            ['id' => 53, 'product_id' => 15, 'product_collection_id' => 13, 'created_at' => null, 'updated_at' => null],
            ['id' => 54, 'product_id' => 16, 'product_collection_id' => 12, 'created_at' => null, 'updated_at' => null],
        ]);
    }
}