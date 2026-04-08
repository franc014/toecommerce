<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

// use App\Models\User;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

// use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*  $user = User::factory()->create([
             'name' => 'Admin',
             'email' => 'jfandtec@gmail.com',
             // password=password
         ]);

         $exitCode = Artisan::call('shield:super-admin', [
             '--user' => $user->id,
             '--panel' => 'admin',
         ]); */

        $this->call([
            /* ProductSeeder::class, */
            /*  TaxSeeder::class, */
            ShieldSeeder::class,
        ]);
    }
}
