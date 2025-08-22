<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Location;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'customer_id' => Customer::factory(),
            'customer_name' => $this->faker->name(),
            'location_id' => Location::factory(),
            'order_number' => strtoupper($this->faker->unique()->bothify('ORD###??')),
            'type' => $this->faker->randomElement(['sale', 'return', 'quote', 'layby']),
            'status' => $this->faker->randomElement(['draft', 'confirmed', 'processing', 'completed', 'cancelled', 'refunded']),
            'subtotal' => $this->faker->randomFloat(2, 50, 500),
            'discount_total' => $this->faker->randomFloat(2, 0, 50),
            'tax_total' => $this->faker->randomFloat(2, 5, 30),
            'total' => $this->faker->randomFloat(2, 60, 600),
            'paid_amount' => $this->faker->randomFloat(2, 0, 600),
            'balance_due' => $this->faker->randomFloat(2, 0, 200),
            'source' => $this->faker->randomElement(['pos', 'online', 'phone', 'mobile']),
            'notes' => $this->faker->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
