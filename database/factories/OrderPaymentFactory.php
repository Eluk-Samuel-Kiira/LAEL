<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderPayment>
 */
class OrderPaymentFactory extends Factory
{
    protected $model = \App\Models\OrderPayment::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $method = $this->faker->randomElement([
            'cash', 'credit_card', 'debit_card', 'gift_card',
            'store_credit', 'bank_transfer', 'mobile', 'other'
        ]);

        return [
            'order_id' => Order::factory(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'payment_method' => $method,
            'transaction_id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'card_last_four' => $method === 'credit_card' || $method === 'debit_card'
                ? $this->faker->numerify('####')
                : null,
            'card_brand' => $method === 'credit_card' || $method === 'debit_card'
                ? $this->faker->randomElement(['Visa', 'Mastercard', 'Amex'])
                : null,
            'notes' => $this->faker->sentence(),
            'processed_by' => User::query()->inRandomOrder()->value('id'),
        ];
    }
}
