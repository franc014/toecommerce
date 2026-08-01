<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tags')->insert([
            ['id' => 65, 'name' => '{"es":"impermeable"}', 'slug' => '{"es":"impermeable"}', 'type' => null, 'order_column' => 1, 'created_at' => '2025-11-28 23:45:58', 'updated_at' => '2025-11-28 23:45:58'],
            ['id' => 66, 'name' => '{"es":"frío"}', 'slug' => '{"es":"frio"}', 'type' => null, 'order_column' => 2, 'created_at' => '2025-11-28 23:45:58', 'updated_at' => '2025-11-28 23:45:58'],
            ['id' => 67, 'name' => '{"es":"abrigo"}', 'slug' => '{"es":"abrigo"}', 'type' => null, 'order_column' => 3, 'created_at' => '2025-11-28 23:45:58', 'updated_at' => '2025-11-28 23:45:58'],
            ['id' => 68, 'name' => '{"es":"invierno"}', 'slug' => '{"es":"invierno"}', 'type' => null, 'order_column' => 4, 'created_at' => '2025-11-28 23:45:58', 'updated_at' => '2025-11-28 23:45:58'],
            ['id' => 69, 'name' => '{"es":"reflectante"}', 'slug' => '{"es":"reflectante"}', 'type' => null, 'order_column' => 5, 'created_at' => '2025-11-28 23:45:58', 'updated_at' => '2025-11-28 23:45:58'],
            ['id' => 70, 'name' => '{"es":"poncho"}', 'slug' => '{"es":"poncho"}', 'type' => null, 'order_column' => 6, 'created_at' => '2025-12-02 00:09:18', 'updated_at' => '2025-12-02 00:09:18'],
            ['id' => 71, 'name' => '{"es":"lluvia"}', 'slug' => '{"es":"lluvia"}', 'type' => null, 'order_column' => 7, 'created_at' => '2025-12-02 00:09:18', 'updated_at' => '2025-12-02 00:09:18'],
            ['id' => 72, 'name' => '{"es":"ligero"}', 'slug' => '{"es":"ligero"}', 'type' => null, 'order_column' => 8, 'created_at' => '2025-12-02 00:09:18', 'updated_at' => '2025-12-02 00:09:18'],
            ['id' => 73, 'name' => '{"es":"chuvasquero"}', 'slug' => '{"es":"chuvasquero"}', 'type' => null, 'order_column' => 9, 'created_at' => '2025-12-02 00:09:18', 'updated_at' => '2025-12-02 00:09:18'],
            ['id' => 74, 'name' => '{"es":"PVC"}', 'slug' => '{"es":"pvc"}', 'type' => null, 'order_column' => 10, 'created_at' => '2025-12-02 00:09:18', 'updated_at' => '2025-12-02 00:09:18'],
            ['id' => 75, 'name' => '{"es":"cortavientos"}', 'slug' => '{"es":"cortavientos"}', 'type' => null, 'order_column' => 11, 'created_at' => '2025-12-02 00:11:38', 'updated_at' => '2025-12-02 00:11:38'],
            ['id' => 76, 'name' => '{"es":"exterior"}', 'slug' => '{"es":"exterior"}', 'type' => null, 'order_column' => 12, 'created_at' => '2025-12-02 00:11:38', 'updated_at' => '2025-12-02 00:11:38'],
            ['id' => 77, 'name' => '{"es":"aventura"}', 'slug' => '{"es":"aventura"}', 'type' => null, 'order_column' => 13, 'created_at' => '2025-12-02 00:11:38', 'updated_at' => '2025-12-02 00:11:38'],
            ['id' => 78, 'name' => '{"es":"nylon"}', 'slug' => '{"es":"nylon"}', 'type' => null, 'order_column' => 14, 'created_at' => '2025-12-02 00:11:38', 'updated_at' => '2025-12-02 00:11:38'],
            ['id' => 79, 'name' => '{"es":"abrigador"}', 'slug' => '{"es":"abrigador"}', 'type' => null, 'order_column' => 15, 'created_at' => '2025-12-02 02:05:37', 'updated_at' => '2025-12-02 02:05:37'],
            ['id' => 80, 'name' => '{"es":"chaleco"}', 'slug' => '{"es":"chaleco"}', 'type' => null, 'order_column' => 16, 'created_at' => '2025-12-02 02:05:37', 'updated_at' => '2025-12-02 02:05:37'],
            ['id' => 81, 'name' => '{"es":"térmico"}', 'slug' => '{"es":"termico"}', 'type' => null, 'order_column' => 17, 'created_at' => '2025-12-02 02:05:37', 'updated_at' => '2025-12-02 02:05:37'],
            ['id' => 82, 'name' => '{"es":"nieve"}', 'slug' => '{"es":"nieve"}', 'type' => null, 'order_column' => 18, 'created_at' => '2025-12-02 02:05:37', 'updated_at' => '2025-12-02 02:05:37'],
            ['id' => 83, 'name' => '{"es":"pijama"}', 'slug' => '{"es":"pijama"}', 'type' => null, 'order_column' => 19, 'created_at' => '2025-12-02 02:08:44', 'updated_at' => '2025-12-02 02:08:44'],
            ['id' => 84, 'name' => '{"es":"cómodo"}', 'slug' => '{"es":"comodo"}', 'type' => null, 'order_column' => 20, 'created_at' => '2025-12-02 02:08:44', 'updated_at' => '2025-12-02 02:08:44'],
            ['id' => 85, 'name' => '{"es":"jersey"}', 'slug' => '{"es":"jersey"}', 'type' => null, 'order_column' => 21, 'created_at' => '2025-12-02 02:08:44', 'updated_at' => '2025-12-02 02:08:44'],
            ['id' => 86, 'name' => '{"es":"anti-pelo"}', 'slug' => '{"es":"anti-pelo"}', 'type' => null, 'order_column' => 22, 'created_at' => '2025-12-02 02:08:44', 'updated_at' => '2025-12-02 02:08:44'],
            ['id' => 87, 'name' => '{"es":"suave"}', 'slug' => '{"es":"suave"}', 'type' => null, 'order_column' => 23, 'created_at' => '2025-12-02 02:08:44', 'updated_at' => '2025-12-02 02:08:44'],
            ['id' => 88, 'name' => '{"es":"toalla"}', 'slug' => '{"es":"toalla"}', 'type' => null, 'order_column' => 24, 'created_at' => '2025-12-02 02:12:16', 'updated_at' => '2025-12-02 02:12:16'],
            ['id' => 89, 'name' => '{"es":"bata"}', 'slug' => '{"es":"bata"}', 'type' => null, 'order_column' => 25, 'created_at' => '2025-12-02 02:12:16', 'updated_at' => '2025-12-02 02:12:16'],
            ['id' => 90, 'name' => '{"es":"secado"}', 'slug' => '{"es":"secado"}', 'type' => null, 'order_column' => 26, 'created_at' => '2025-12-02 02:12:16', 'updated_at' => '2025-12-02 02:12:16'],
            ['id' => 91, 'name' => '{"es":"baño"}', 'slug' => '{"es":"bano"}', 'type' => null, 'order_column' => 27, 'created_at' => '2025-12-02 02:12:16', 'updated_at' => '2025-12-02 02:12:16'],
            ['id' => 92, 'name' => '{"es":"mono"}', 'slug' => '{"es":"mono"}', 'type' => null, 'order_column' => 28, 'created_at' => '2025-12-02 02:14:15', 'updated_at' => '2025-12-02 02:14:15'],
            ['id' => 93, 'name' => '{"es":"shedding"}', 'slug' => '{"es":"shedding"}', 'type' => null, 'order_column' => 29, 'created_at' => '2025-12-02 02:14:15', 'updated_at' => '2025-12-02 02:14:15'],
            ['id' => 94, 'name' => '{"es":"relajación"}', 'slug' => '{"es":"relajacion"}', 'type' => null, 'order_column' => 30, 'created_at' => '2025-12-02 02:14:15', 'updated_at' => '2025-12-02 02:14:15'],
            ['id' => 95, 'name' => '{"es":"terciopelo"}', 'slug' => '{"es":"terciopelo"}', 'type' => null, 'order_column' => 31, 'created_at' => '2025-12-02 02:18:34', 'updated_at' => '2025-12-02 02:18:34'],
            ['id' => 96, 'name' => '{"es":"lujo"}', 'slug' => '{"es":"lujo"}', 'type' => null, 'order_column' => 32, 'created_at' => '2025-12-02 02:18:34', 'updated_at' => '2025-12-02 02:18:34'],
            ['id' => 97, 'name' => '{"es":"lounge"}', 'slug' => '{"es":"lounge"}', 'type' => null, 'order_column' => 33, 'created_at' => '2025-12-02 02:18:34', 'updated_at' => '2025-12-02 02:18:34'],
            ['id' => 98, 'name' => '{"es":"dos piezas"}', 'slug' => '{"es":"dos-piezas"}', 'type' => null, 'order_column' => 34, 'created_at' => '2025-12-02 02:18:34', 'updated_at' => '2025-12-02 02:18:34'],
            ['id' => 99, 'name' => '{"es":"elegante"}', 'slug' => '{"es":"elegante"}', 'type' => null, 'order_column' => 35, 'created_at' => '2025-12-02 02:18:34', 'updated_at' => '2025-12-02 02:18:34'],
            ['id' => 100, 'name' => '{"es":"suéter"}', 'slug' => '{"es":"sueter"}', 'type' => null, 'order_column' => 36, 'created_at' => '2025-12-02 02:21:22', 'updated_at' => '2025-12-02 02:21:22'],
            ['id' => 101, 'name' => '{"es":"punto"}', 'slug' => '{"es":"punto"}', 'type' => null, 'order_column' => 37, 'created_at' => '2025-12-02 02:21:22', 'updated_at' => '2025-12-02 02:21:22'],
            ['id' => 102, 'name' => '{"es":"básico"}', 'slug' => '{"es":"basico"}', 'type' => null, 'order_column' => 38, 'created_at' => '2025-12-02 02:21:22', 'updated_at' => '2025-12-02 02:21:22'],
            ['id' => 103, 'name' => '{"es":"casual"}', 'slug' => '{"es":"casual"}', 'type' => null, 'order_column' => 39, 'created_at' => '2025-12-02 02:21:22', 'updated_at' => '2025-12-02 02:21:22'],
            ['id' => 104, 'name' => '{"es":"diario"}', 'slug' => '{"es":"diario"}', 'type' => null, 'order_column' => 40, 'created_at' => '2025-12-02 02:21:22', 'updated_at' => '2025-12-02 02:21:22'],
            ['id' => 105, 'name' => '{"es":"navidad"}', 'slug' => '{"es":"navidad"}', 'type' => null, 'order_column' => 41, 'created_at' => '2025-12-02 02:26:42', 'updated_at' => '2025-12-02 02:26:42'],
            ['id' => 106, 'name' => '{"es":"santa"}', 'slug' => '{"es":"santa"}', 'type' => null, 'order_column' => 42, 'created_at' => '2025-12-02 02:26:42', 'updated_at' => '2025-12-02 02:26:42'],
            ['id' => 107, 'name' => '{"es":"disfraz"}', 'slug' => '{"es":"disfraz"}', 'type' => null, 'order_column' => 43, 'created_at' => '2025-12-02 02:26:42', 'updated_at' => '2025-12-02 02:26:42'],
            ['id' => 108, 'name' => '{"es":"festivo"}', 'slug' => '{"es":"festivo"}', 'type' => null, 'order_column' => 44, 'created_at' => '2025-12-02 02:26:42', 'updated_at' => '2025-12-02 02:26:42'],
            ['id' => 109, 'name' => '{"es":"halloween"}', 'slug' => '{"es":"halloween"}', 'type' => null, 'order_column' => 45, 'created_at' => '2025-12-02 02:30:02', 'updated_at' => '2025-12-02 02:30:02'],
            ['id' => 110, 'name' => '{"es":"misterioso"}', 'slug' => '{"es":"misterioso"}', 'type' => null, 'order_column' => 46, 'created_at' => '2025-12-02 02:30:02', 'updated_at' => '2025-12-02 02:30:02'],
            ['id' => 111, 'name' => '{"es":"diciembre"}', 'slug' => '{"es":"diciembre"}', 'type' => null, 'order_column' => 47, 'created_at' => '2025-12-02 02:32:02', 'updated_at' => '2025-12-02 02:32:02'],
            ['id' => 112, 'name' => '{"es":"papá noel"}', 'slug' => '{"es":"papa-noel"}', 'type' => null, 'order_column' => 48, 'created_at' => '2025-12-02 02:32:02', 'updated_at' => '2025-12-02 02:32:02'],
            ['id' => 113, 'name' => '{"es":"hook"}', 'slug' => '{"es":"hook"}', 'type' => null, 'order_column' => 49, 'created_at' => '2025-12-02 02:34:28', 'updated_at' => '2025-12-02 02:34:28'],
            ['id' => 114, 'name' => '{"es":"san valentín"}', 'slug' => '{"es":"san-valentin"}', 'type' => null, 'order_column' => 50, 'created_at' => '2025-12-02 02:34:28', 'updated_at' => '2025-12-02 02:34:28'],
            ['id' => 115, 'name' => '{"es":"happy"}', 'slug' => '{"es":"happy"}', 'type' => null, 'order_column' => 51, 'created_at' => '2025-12-02 02:34:28', 'updated_at' => '2025-12-02 02:34:28'],
            ['id' => 116, 'name' => '{"es":"feliz"}', 'slug' => '{"es":"feliz"}', 'type' => null, 'order_column' => 52, 'created_at' => '2025-12-02 02:34:28', 'updated_at' => '2025-12-02 02:34:28'],
            ['id' => 117, 'name' => '{"es":"buso"}', 'slug' => '{"es":"buso"}', 'type' => null, 'order_column' => 53, 'created_at' => '2025-12-02 02:37:18', 'updated_at' => '2025-12-02 02:37:18'],
            ['id' => 118, 'name' => '{"es":"camiseta"}', 'slug' => '{"es":"camiseta"}', 'type' => null, 'order_column' => 54, 'created_at' => '2025-12-02 02:37:18', 'updated_at' => '2025-12-02 02:37:18'],
            ['id' => 119, 'name' => '{"es":"outdoors"}', 'slug' => '{"es":"outdoors"}', 'type' => null, 'order_column' => 55, 'created_at' => '2025-12-02 02:37:18', 'updated_at' => '2025-12-02 02:37:18'],
            ['id' => 120, 'name' => '{"es":"salidas"}', 'slug' => '{"es":"salidas"}', 'type' => null, 'order_column' => 56, 'created_at' => '2025-12-02 02:37:18', 'updated_at' => '2025-12-02 02:37:18'],
            ['id' => 121, 'name' => '{"es":"relax"}', 'slug' => '{"es":"relax"}', 'type' => null, 'order_column' => 57, 'created_at' => '2025-12-02 02:41:00', 'updated_at' => '2025-12-02 02:41:00'],
            ['id' => 122, 'name' => '{"es":"transpirable"}', 'slug' => '{"es":"transpirable"}', 'type' => null, 'order_column' => 58, 'created_at' => '2025-12-02 02:43:16', 'updated_at' => '2025-12-02 02:43:16'],
        ]);
    }
}