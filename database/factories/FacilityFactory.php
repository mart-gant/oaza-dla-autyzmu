<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Facility;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facility>
 */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    { 
        $faker = $this->faker ?? \Faker\Factory::create();

        return [
            'user_id' => User::factory(),
            'name' => $faker->company,
            'type' => $faker->randomElement(['szkola', 'przedszkole', 'osrodek_terapeutyczny', 'poradnia', 'fundacja', 'stowarzyszenie', 'inne']),
            'description' => $faker->text,
            'address' => $faker->address,
            'city' => $faker->city,
            'province' => $faker->state,
            'postal_code' => $faker->postcode,
            'phone' => $faker->phoneNumber,
            'email' => $faker->unique()->safeEmail,
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
            'available_spots' => $faker->numberBetween(0, 100),
        ];
    }
}
