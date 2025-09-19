<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'jfandtec@gmail.com',
            // password=password
        ]);

        // making super admin with filament shield command
        // Artisan::call('shield:super-admin', ['--user' => $user->id]);
    }
}
