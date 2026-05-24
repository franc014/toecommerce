<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

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
