<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountablesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('discountables')->insert([
            ['id' => 1, 'discount_id' => 1, 'discountable_id' => 1, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 2, 'discount_id' => 1, 'discountable_id' => 2, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 3, 'discount_id' => 1, 'discountable_id' => 3, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 4, 'discount_id' => 1, 'discountable_id' => 4, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 5, 'discount_id' => 1, 'discountable_id' => 5, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 6, 'discount_id' => 1, 'discountable_id' => 6, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 7, 'discount_id' => 1, 'discountable_id' => 7, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 8, 'discount_id' => 1, 'discountable_id' => 8, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 9, 'discount_id' => 1, 'discountable_id' => 9, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 10, 'discount_id' => 1, 'discountable_id' => 10, 'discountable_type' => 'App\Models\Product', 'created_at' => null, 'updated_at' => null],
            ['id' => 11, 'discount_id' => 1, 'discountable_id' => 27, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 12, 'discount_id' => 1, 'discountable_id' => 28, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 13, 'discount_id' => 1, 'discountable_id' => 29, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 14, 'discount_id' => 1, 'discountable_id' => 30, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 15, 'discount_id' => 1, 'discountable_id' => 31, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 16, 'discount_id' => 1, 'discountable_id' => 32, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 17, 'discount_id' => 1, 'discountable_id' => 33, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 18, 'discount_id' => 1, 'discountable_id' => 34, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 19, 'discount_id' => 1, 'discountable_id' => 35, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
            ['id' => 20, 'discount_id' => 1, 'discountable_id' => 36, 'discountable_type' => 'App\Models\ProductVariant', 'created_at' => null, 'updated_at' => null],
        ]);
    }
}