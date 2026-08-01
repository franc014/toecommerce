<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('menu_items')->insert([
            ['id' => 10, 'menu_id' => 1, 'slug' => 'products', 'label' => 'Productos', 'url' => '/products', 'items' => null, 'order_column' => 1, 'created_at' => '2025-11-28 20:15:49', 'updated_at' => '2025-12-18 23:59:47'],
            ['id' => 11, 'menu_id' => 2, 'slug' => 'todos-los-productos', 'label' => 'Todos los Productos', 'url' => '/products', 'items' => null, 'order_column' => 1, 'created_at' => '2025-12-02 02:51:41', 'updated_at' => '2025-12-19 00:00:18'],
            ['id' => 12, 'menu_id' => 2, 'slug' => 'busca-por-colecciones', 'label' => 'Busca por Colecciones', 'url' => '/collections', 'items' => null, 'order_column' => 3, 'created_at' => '2025-12-02 02:52:14', 'updated_at' => '2025-12-19 00:00:18'],
            ['id' => 13, 'menu_id' => 2, 'slug' => 'nuevas-prendas', 'label' => 'Nuevas prendas', 'url' => '/products', 'items' => null, 'order_column' => 2, 'created_at' => '2025-12-02 02:52:26', 'updated_at' => '2025-12-19 00:00:18'],
            ['id' => 14, 'menu_id' => 3, 'slug' => 'terminos-y-condiciones', 'label' => 'Términos y Condiciones', 'url' => '/terminos-y-condiciones', 'items' => null, 'order_column' => 5, 'created_at' => '2025-12-02 02:53:46', 'updated_at' => '2025-12-19 00:00:48'],
            ['id' => 15, 'menu_id' => 3, 'slug' => 'politica-de-privacidad', 'label' => 'Politica de privacidad', 'url' => '/politica-de-privacidad', 'items' => null, 'order_column' => 6, 'created_at' => '2025-12-02 02:53:59', 'updated_at' => '2025-12-19 00:00:48'],
            ['id' => 16, 'menu_id' => 1, 'slug' => 'colecciones', 'label' => 'Colecciones', 'url' => '/collections', 'items' => null, 'order_column' => 7, 'created_at' => '2025-12-18 23:59:47', 'updated_at' => '2025-12-18 23:59:47'],
            ['id' => 17, 'menu_id' => 1, 'slug' => 'acerca-de', 'label' => 'Acerca De', 'url' => '/about', 'items' => null, 'order_column' => 8, 'created_at' => '2025-12-18 23:59:47', 'updated_at' => '2025-12-18 23:59:47'],
            ['id' => 18, 'menu_id' => 1, 'slug' => 'contacto', 'label' => 'Contacto', 'url' => '/contact', 'items' => null, 'order_column' => 9, 'created_at' => '2025-12-18 23:59:47', 'updated_at' => '2025-12-18 23:59:47'],
        ]);
    }
}