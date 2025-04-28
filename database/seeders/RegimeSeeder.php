<?php

namespace Database\Seeders;

use App\Models\Regime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      Regime::insert([
        ['description' => 'Café da Manhã', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ['description' => 'All Inclusive', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
      ]);
    }
}
