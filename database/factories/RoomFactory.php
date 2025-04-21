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
        return [
            "name" => "Room " . $this->faker->unique()->numberBetween(1, 60),
            "class_id" => fake()->numberBetween(1, 5),
            "price" => fake()->randomFloat(2, 15.99, 46.88, ),
            "isBooked" => fake()->boolean(50)
        ];
    }
}
