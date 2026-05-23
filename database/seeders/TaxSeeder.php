<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use JFA\ToecommerceCore\Models\Tax;

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
