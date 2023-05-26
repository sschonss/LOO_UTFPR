<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Models\Client;
use App\Models\Seller;
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
            'seller_id' => Seller::factory(),
            'client_id' => Client::factory(),
            'sold_at' => $this->faker->dateTimeBetween('-8 year', 'now'),
            'total_amount' => $this->faker->numberBetween(10000, 10000000),
            'status' => $this->faker->randomElement(Status::cases()),
        ];
    }
}
