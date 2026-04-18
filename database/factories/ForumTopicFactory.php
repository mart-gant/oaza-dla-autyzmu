<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumTopic>
 */
class ForumTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = $this->faker ?? \Faker\Factory::create();

        return [
            'forum_category_id' => ForumCategory::factory(),
            'user_id' => User::factory(),
            'title' => $faker->sentence(),
        ];
    }
}
