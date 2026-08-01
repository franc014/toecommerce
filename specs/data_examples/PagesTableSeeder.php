<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pages')->insert([
            ['id' => 1, 'title' => 'Home', 'slug' => 'home', 'description' => 'Home Page', 'status' => 'published', 'metatags' => '{"og_title":"","og_description":"","og_image":"","twitter_card":"summary_large_image","twitter_title":"","twitter_description":"","twitter_image":"","robots":"index,follow"}', 'route' => 'home', 'published_at' => '2025-11-23 00:59:43', 'created_at' => '2025-11-23 00:59:36', 'updated_at' => '2025-11-23 00:59:43'],
            ['id' => 2, 'title' => 'Acerca de', 'slug' => 'acerca-de', 'description' => 'Página sobre la empresa', 'status' => 'published', 'metatags' => '{"og_title":"","og_description":"","og_image":"","twitter_card":"summary_large_image","twitter_title":"","twitter_description":"","twitter_image":"","robots":"index,follow"}', 'route' => 'acerca-de', 'published_at' => '2025-12-13 17:57:22', 'created_at' => '2025-12-18 17:34:55', 'updated_at' => '2025-12-18 17:34:55'],
            ['id' => 3, 'title' => 'Términos y condiciones', 'slug' => 'terminos-y-condiciones', 'description' => 'Página de términos y condiciones', 'status' => 'published', 'metatags' => '{"og_title":"","og_description":"","og_image":"","twitter_card":"summary_large_image","twitter_title":"","twitter_description":"","twitter_image":"","robots":"index,follow"}', 'route' => 'terminos-y-condiciones', 'published_at' => '2025-12-17 17:25:52', 'created_at' => '2025-12-18 17:34:55', 'updated_at' => '2025-12-18 17:34:55'],
            ['id' => 4, 'title' => 'Política de Privacidad', 'slug' => 'politica-de-privacidad', 'description' => 'Página de Política de Privacidad', 'status' => 'published', 'metatags' => '{"og_title":"","og_description":"","og_image":"","twitter_card":"summary_large_image","twitter_title":"","twitter_description":"","twitter_image":"","robots":"index,follow"}', 'route' => 'politica-de-privacidad', 'published_at' => '2025-12-17 17:25:53', 'created_at' => '2025-12-18 17:34:55', 'updated_at' => '2025-12-18 17:34:55'],
            ['id' => 5, 'title' => 'Contact', 'slug' => 'contact', 'description' => 'Contact Page', 'status' => 'published', 'metatags' => '{"og_title":"","og_description":"","og_image":"","twitter_card":"summary_large_image","twitter_title":"","twitter_description":"","twitter_image":"","robots":"index,follow"}', 'route' => 'contact', 'published_at' => '2025-12-29 23:00:10', 'created_at' => '2025-12-31 00:06:31', 'updated_at' => '2025-12-31 00:06:31'],
            ['id' => 6, 'title' => 'Products', 'slug' => 'products', 'description' => 'Products Page', 'status' => 'published', 'metatags' => '{"og_title":"","og_description":"","og_image":"","twitter_card":"summary_large_image","twitter_title":"","twitter_description":"","twitter_image":"","robots":"index,follow"}', 'route' => 'products', 'published_at' => '2025-12-30 00:30:47', 'created_at' => '2025-12-31 00:06:31', 'updated_at' => '2025-12-31 00:06:31'],
            ['id' => 7, 'title' => 'Collections', 'slug' => 'collections', 'description' => 'Collections Page', 'status' => 'published', 'metatags' => '{"og_title":"","og_description":"","og_image":"","twitter_card":"summary_large_image","twitter_title":"","twitter_description":"","twitter_image":"","robots":"index,follow"}', 'route' => 'collections', 'published_at' => '2025-12-30 01:18:39', 'created_at' => '2025-12-31 00:06:31', 'updated_at' => '2025-12-31 00:06:31'],
        ]);
    }
}