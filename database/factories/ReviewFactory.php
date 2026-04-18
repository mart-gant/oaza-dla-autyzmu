<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Review;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = $this->faker ?? \Faker\Factory::create();

        return [
            'user_id' => \App\Models\User::factory(),
            'facility_id' => \App\Models\Facility::factory(),
            'rating' => $faker->numberBetween(1, 5),
            'comment' => $faker->sentence,
        ];
    }
}
