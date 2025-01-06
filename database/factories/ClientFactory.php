<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $faker = FakerFactory::create('fr_FR');

        return [
            'nom' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'adresse' => fake()->address(),
            'code_postal' => fake()->countryCode(),
            'ville' => fake()->city(),
            'telephone' => fake()->phoneNumber(),
            'siret' => substr($faker->siret(false), 0, 14),
        ];
    }
}
