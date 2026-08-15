<?php

namespace Database\Factories;

use App\Models\Brag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Brag>
 */
class BragFactory extends Factory
{
    protected $model = Brag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'context' => fake()->paragraph(),
            'impact' => fake()->sentence(6),
            'technologies' => [fake()->word(), fake()->word()],
        ];
    }
}
