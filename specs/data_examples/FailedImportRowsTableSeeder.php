<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FailedImportRowsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('failed_import_rows')->insert([
            ['id' => 5, 'data' => '{"ID":"10","Menu id":"1","Slug":"products","Label":"Productos","Url":"\/products","Items":"","Order column":"1","Created at":"2025-11-28 20:15:49","Updated at":"2025-11-28 20:15:49"}', 'import_id' => 8, 'validation_error' => 'The items field is required.', 'created_at' => '2025-12-18 23:54:35', 'updated_at' => '2025-12-18 23:54:35'],
            ['id' => 6, 'data' => '{"ID":"16","Menu id":"1","Slug":"colecciones","Label":"Colecciones","Url":"\/collections","Items":"","Order column":"7","Created at":"2025-12-10 01:10:46","Updated at":"2025-12-10 01:10:46"}', 'import_id' => 8, 'validation_error' => 'The items field is required.', 'created_at' => '2025-12-18 23:54:35', 'updated_at' => '2025-12-18 23:54:35'],
            ['id' => 7, 'data' => '{"ID":"17","Menu id":"1","Slug":"acerca-de","Label":"Acerca De","Url":"\/about","Items":"","Order column":"8","Created at":"2025-12-13 17:17:30","Updated at":"2025-12-13 17:17:30"}', 'import_id' => 8, 'validation_error' => 'The items field is required.', 'created_at' => '2025-12-18 23:54:35', 'updated_at' => '2025-12-18 23:54:35'],
            ['id' => 8, 'data' => '{"ID":"18","Menu id":"1","Slug":"contacto","Label":"Contacto","Url":"\/contact","Items":"","Order column":"9","Created at":"2025-12-16 03:00:43","Updated at":"2025-12-16 03:00:43"}', 'import_id' => 8, 'validation_error' => 'The items field is required.', 'created_at' => '2025-12-18 23:54:35', 'updated_at' => '2025-12-18 23:54:35'],
        ]);
    }
}