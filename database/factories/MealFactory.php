<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(10),
            'category_id' => $this->faker->numberBetween(1,50),
            'price' => $this->faker->randomFloat(2 , 5 , 1000),
            'calories' => $this->faker->randomFloat(2 , 0 , 1500),
            'estimated_time' => $this->faker->time('h:i:s')
        ];
    }
}
