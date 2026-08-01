<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['id' => 1, 'title' => 'Ropa de Abrigo', 'description' => 'Abrigos, chaquetas y capas para proteger del frío', 'slug' => 'ropa-de-abrigo', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 2, 'title' => 'Ropa de Dormir', 'description' => 'Pijamas, batas y ropa cómoda para descansar', 'slug' => 'ropa-de-dormir', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 3, 'title' => 'Ropa de Playa', 'description' => 'Buzos, monos y ropa ligera para el verano', 'slug' => 'ropa-de-playa', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 4, 'title' => 'Ropa de Invierno', 'description' => 'Suéteres, jerseys y prendas térmicas para el frío', 'slug' => 'ropa-de-invierno', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 5, 'title' => 'Disfraces', 'description' => 'Disfraces temáticos para Halloween, Navidad y San Valentín', 'slug' => 'disfraces', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 6, 'title' => 'Accesorios', 'description' => 'Accesorios y complementos para tu mascota', 'slug' => 'accesorios', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 7, 'title' => 'Ofertas', 'description' => 'Productos en oferta y descuentos especiales', 'slug' => 'ofertas', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 8, 'title' => 'Novedades', 'description' => 'Las últimas novedades en ropa para mascotas', 'slug' => 'novedades', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 9, 'title' => 'Outlet', 'description' => 'Productos de outlet con descuentos especiales', 'slug' => 'outlet', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
            ['id' => 10, 'title' => 'Lo más vendido', 'description' => 'Los productos más vendidos de nuestra tienda', 'slug' => 'lo-mas-vendido', 'created_at' => '2025-11-23 00:41:49', 'updated_at' => '2025-11-23 00:41:49'],
        ]);
    }
}