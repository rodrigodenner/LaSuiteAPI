<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@webav.com.br',
            'password' => bcrypt('110308dj'),
        ]);

      $this->call([
        RegimeSeeder::class,
        RoomSeeder::class,
      ]);
    }
}
