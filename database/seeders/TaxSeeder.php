<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tax::factory()->create([
            'name' => 'IVA',
            'percentage' => 15,
            'description' => 'Impuesto al valor agregado',
        ]);
    }
}
