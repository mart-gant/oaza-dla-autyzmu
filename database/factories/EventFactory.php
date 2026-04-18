<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'start_date' => $this->faker->dateTimeBetween('+1 days', '+30 days'),
            'end_date' => $this->faker->dateTimeBetween('+1 days', '+30 days'),
            'location' => $this->faker->city(),
            'facility_id' => null,
            'user_id' => User::factory(),
            'is_public' => $this->faker->boolean(80), // 80% publiczne
        ];
    }
}
