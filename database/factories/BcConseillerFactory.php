<?php

namespace Database\Factories;

use App\Models\BcConseiller;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BcConseillerFactory extends Factory
{

    protected $model = BcConseiller::class;

    public function definition(): array
    {
        return [
            'nom_complet' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'telephone' => fake()->phoneNumber(),
        ];
    }
}
