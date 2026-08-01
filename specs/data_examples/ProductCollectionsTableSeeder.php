<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCollectionsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_collections')->insert([
            ['id' => 11, 'title' => 'Adventure Gear', 'description' => 'Protección contra la lluvia, el viento y el frío durante actividades al aire libre.', 'slug' => 'adventure-gear', 'featured_image' => 'images/01KBEF83W1M0J3DGGSRRPKXR08.jpg', 'created_at' => '2025-11-26 16:33:19', 'updated_at' => '2025-12-02 02:46:14'],
            ['id' => 12, 'title' => 'Sleep & Lounge', 'description' => 'Prendas suaves, cómodas y a menudo ligeras para la hora de dormir y para relajarse.', 'slug' => 'sleep-lounge', 'featured_image' => 'images/01KBEF9HKW8WMDQD2QPG6NZK1B.jpg', 'created_at' => '2025-11-26 16:36:57', 'updated_at' => '2025-12-02 02:47:00'],
            ['id' => 13, 'title' => 'Everyday Cozy', 'description' => 'La colección principal de artículos cómodos y fáciles de llevar.', 'slug' => 'everyday-cozy', 'featured_image' => 'images/01KBEFARYFC84WVB4ZBS3235J2.jpg', 'created_at' => '2025-11-26 16:42:59', 'updated_at' => '2025-12-02 02:47:41'],
            ['id' => 14, 'title' => 'Seasonal, Holiday', 'description' => 'Disfraces de Halloween, suéteres navideños, pañuelos de Pascua, camisas hawaianas de verano.', 'slug' => 'seasonal-holiday', 'featured_image' => 'images/01KBEFBYFMNHEEHW20KZH1RZQF.jpg', 'created_at' => '2025-11-26 16:51:12', 'updated_at' => '2025-12-02 02:48:19'],
        ]);
    }
}