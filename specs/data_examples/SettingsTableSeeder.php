<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->insert([
            ['id' => 1, 'group' => 'storefront', 'name' => 'products_per_page', 'locked' => false, 'payload' => '5', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2026-04-04 21:20:35'],
            ['id' => 2, 'group' => 'storefront', 'name' => 'stock_control_mode', 'locked' => false, 'payload' => '"none"', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2026-04-04 21:20:35'],
            ['id' => 3, 'group' => 'company', 'name' => 'name', 'locked' => false, 'payload' => '"ToEcommerce"', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2025-11-28 00:40:48'],
            ['id' => 4, 'group' => 'company', 'name' => 'email', 'locked' => false, 'payload' => '"jfandtec@gmail.com"', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2025-11-28 00:40:48'],
            ['id' => 5, 'group' => 'company', 'name' => 'phone', 'locked' => false, 'payload' => '"593968741465"', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2025-11-28 00:40:48'],
            ['id' => 6, 'group' => 'company', 'name' => 'whatsapp', 'locked' => false, 'payload' => '"593968741465"', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2025-11-28 00:40:48'],
            ['id' => 7, 'group' => 'company', 'name' => 'address', 'locked' => false, 'payload' => '"Quito, Ecuador"', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2025-11-28 00:40:48'],
            ['id' => 8, 'group' => 'company', 'name' => 'socialMedia', 'locked' => false, 'payload' => '{"facebook":"https:\\/\\/www.facebook.com","instagram":"https:\\/\\/www.instagram.com","twitter":"https:\\/\\/www.twitter.com"}', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2025-11-28 00:40:48'],
            ['id' => 9, 'group' => 'company', 'name' => 'workingDays', 'locked' => false, 'payload' => '{"lunes":"7 AM - 8 PM","martes":"7 AM - 8 PM","miercoles":"9 AM - 8 PM","jueves":"9 AM - 8 PM","viernes":"9 AM - 8 PM","sabado":"9 AM - 12 PM","domingo":"no abrimos"}', 'created_at' => '2025-11-28 00:40:48', 'updated_at' => '2025-11-28 00:40:48'],
            ['id' => 46, 'group' => 'storefront', 'name' => 'discount_calculation_mode', 'locked' => false, 'payload' => '"highest"', 'created_at' => '2026-02-05 19:11:40', 'updated_at' => '2026-04-04 21:20:35'],
            ['id' => 47, 'group' => 'storefront', 'name' => 'show_discount_campaign_message', 'locked' => false, 'payload' => 'false', 'created_at' => '2026-02-05 19:11:40', 'updated_at' => '2026-04-04 21:20:35'],
            ['id' => 48, 'group' => 'storefront', 'name' => 'discount_campaign_message', 'locked' => false, 'payload' => '"Disfruta el 20% de descuento en toda la tienda, con mucho amor \\u2764\\ufe0f\\nDel 1 al 28 de febrero."', 'created_at' => '2026-02-05 19:11:40', 'updated_at' => '2026-04-04 21:20:35'],
        ]);
    }
}