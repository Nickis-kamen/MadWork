<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'first_name' => 'prenom',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'date_of_birth' => '2000-01-01',
            'phone' => '1234567890',
            'role' => 'admin',
            'address' => '123 Admin St, Admin City, Admin Country',
        ]);
    }
}
