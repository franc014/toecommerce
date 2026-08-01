<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserInfoEntriesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_info_entries')->insert([
            ['id' => 1, 'user_id' => 2, 'first_name' => 'Pablo', 'last_name' => 'Palacios', 'type' => 'billing', 'email' => 'jfandradea@gmail.com', 'country' => 'Ecuador', 'state' => 'Pichincha', 'city' => 'Quito', 'address' => 'La Loma de Puengasí', 'phone' => '0968741465', 'zipcode' => '170502', 'is_main' => true, 'created_at' => '2025-12-21 18:29:20', 'updated_at' => '2026-04-09 19:45:22'],
            ['id' => 2, 'user_id' => 2, 'first_name' => 'Pablillo', 'last_name' => 'Palacios', 'type' => 'shipping', 'email' => 'jfandradea@gmail.com', 'country' => 'Ecuador', 'state' => 'Pichincha', 'city' => 'Quito', 'address' => 'La Loma de Puengasí', 'phone' => '0968741465', 'zipcode' => '170502', 'is_main' => true, 'created_at' => '2025-12-21 18:29:36', 'updated_at' => '2025-12-21 18:29:58'],
            ['id' => 3, 'user_id' => 3, 'first_name' => 'Clever', 'last_name' => 'Andrade', 'type' => 'billing', 'email' => 'kvaa77@gmail.com', 'country' => 'Ecuador', 'state' => 'Pichincha', 'city' => 'Quito', 'address' => 'San Camilo', 'phone' => '0939621890', 'zipcode' => '170203', 'is_main' => true, 'created_at' => '2026-01-11 03:31:29', 'updated_at' => '2026-01-11 03:31:29'],
            ['id' => 4, 'user_id' => 3, 'first_name' => 'Clever', 'last_name' => 'Andrade', 'type' => 'shipping', 'email' => 'kvaa77@gmail.com', 'country' => 'Ecuador', 'state' => 'Pichincha', 'city' => 'Quito', 'address' => 'San Camilo', 'phone' => '0939621890', 'zipcode' => '170203', 'is_main' => true, 'created_at' => '2026-01-11 03:31:48', 'updated_at' => '2026-01-11 03:31:48'],
        ]);
    }
}