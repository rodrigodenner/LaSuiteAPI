<?php

namespace Database\Seeders;

use App\Models\Regime;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $regimes = Regime::all();

    Room::factory(20)->create()->each(function ($room) use ($regimes) {
      // ✅ Gerar 5 imagens (1 featured e 4 normais)
      for ($i = 1; $i <= 5; $i++) {
        $room->images()->create([
          'description' => 'Imagem ' . $i . ' do quarto ' . $room->name,
          'alt'         => 'Imagem ' . $i . ' do quarto ' . $room->name,
          'featured'    => $i === 1, // Apenas a primeira imagem é featured
          'image_path'  => 'https://via.placeholder.com/300x200.png?text=Quarto+' . $room->id . '+Imagem+' . $i,
        ]);
      }

      // Disponibilidades para os próximos 10 dias
      foreach (range(0, 9) as $day) {
        $room->availabilities()->create([
          'date'     => now()->addDays($day)->format('Y-m-d'),
          'quantity' => rand(3, 7),
        ]);
      }

      // Tarifas por regime
      foreach ($regimes as $regime) {
        $room->tariffs()->create([
          'regime_id'         => $regime->id,
          'start_date'        => now()->format('Y-m-d'),
          'end_date'          => now()->addDays(9)->format('Y-m-d'),
          'type'              => 'daily',
          'value_room'        => $regime->description === 'Café da Manhã' ? rand(180, 250) : rand(300, 450),
          'additional_adult'  => rand(50, 100),
          'additional_child'  => rand(30, 80),
        ]);
      }
    });
  }
}
