<?php

namespace Database\Factories;

use App\Models\Prospect;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prospect>
 */
class ProspectFactory extends Factory
{
    protected $model = Prospect::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = FakerFactory::create('fr_FR');

        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'postal_code' => fake()->countryCode(),
            'company' => fake()->name(),
            'city' => fake()->city(),
            'phone_number' => fake()->phoneNumber(),
            'siret' => substr($faker->siret(false), 0, 14),
            'gender' => fake()->randomElement(['homme', 'femme']),
        ];
    }
}
