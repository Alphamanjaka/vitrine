<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => 'SALE-' . $this->faker->unique()->numerify('########'),
            'total_brut' => 0, // Sera calculé dynamiquement dans le Seeder
            'discount' => 0,
            'total_net' => 0,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
