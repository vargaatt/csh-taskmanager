<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

         User::factory()->hasTasks(3)->create([
             'name' => 'Admin',
             'email' => 'admin@example.com',
         ]);

        User::factory()->hasTasks(3)->create();
        User::factory()->hasTasks(3)->create();
        User::factory()->hasTasks(3)->create();


    }
}
