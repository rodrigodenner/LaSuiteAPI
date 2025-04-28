<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  public function definition(): array
  {
    $name = 'Quarto ' . $this->faker->unique()->numberBetween(100, 999);

    return [
      'name'           => $name,
      'slug'           => \Str::slug($name),
      'featured'       => $this->faker->boolean(30),
      'description'    => $this->faker->sentence(8),
      'size'           => $this->faker->numberBetween(20, 60) . 'm²',
      'max_adults'     => $this->faker->numberBetween(1, 4),
      'max_children'   => $this->faker->numberBetween(0, 2),
      'double_beds'    => $this->faker->numberBetween(0, 2),
      'single_beds'    => $this->faker->numberBetween(0, 3),
      'floor'          => $this->faker->numberBetween(1, 10),
      'type'           => $this->faker->randomElement(['Standard', 'Deluxe', 'Suíte']),
      'number'         => $this->faker->unique()->numberBetween(100, 999),
    ];
  }
}
