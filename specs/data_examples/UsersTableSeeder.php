<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin',
                'email' => 'jfandtec@gmail.com',
                'phone' => null,
                'email_verified_at' => '2025-11-23 00:41:49',
                'password' => '$2y$12$NQQ4RFJAyDioINAWWyo9Nu.F2G2cIanwr570KPnSTWyEsErWwQPCa',
                'remember_token' => 'B0q6WShzlQtAWAsBrT6CKKLAEl8fKj82b6E52UHghrVl0SztxHFTWHbnUiJv',
                'created_at' => '2025-11-23 00:41:49',
                'updated_at' => '2025-11-23 00:41:49',
            ],
            [
                'id' => 2,
                'name' => 'Becan',
                'email' => 'jfandradea@gmail.com',
                'phone' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$ACG1jMBGA/jQsiZkOWNLa.v5sdRy.dHGZH2L3rPjRRWQgsnQQOsA',
                'remember_token' => null,
                'created_at' => '2025-12-21 18:27:52',
                'updated_at' => '2025-12-21 18:27:52',
            ],
            [
                'id' => 3,
                'name' => 'Clever Andrade',
                'email' => 'kvaa77@gmail.com',
                'phone' => null,
                'email_verified_at' => null,
                'password' => '$2y$12$tebPojm9GlRiRkJHT/Smtu1eG6r.C530UxmxrunQf2gJn.ouaNgoi',
                'remember_token' => null,
                'created_at' => '2026-01-11 03:30:23',
                'updated_at' => '2026-01-11 03:30:23',
            ],
        ]);
    }
}