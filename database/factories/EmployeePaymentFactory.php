<?php
// database/factories/EmployeePaymentFactory.php

namespace Database\Factories;

use App\Models\EmployeePayment;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeePaymentFactory extends Factory
{
    protected $model = EmployeePayment::class;

    public function definition(): array
    {
        $paymentDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $paymentType = $this->faker->randomElement(EmployeePayment::getPaymentTypes());
        
        return [
            'employee_id' => Employee::factory(),
            'tenant_id' => fn (array $attributes) => Employee::find($attributes['employee_id'])->tenant_id,
            'payment_date' => $paymentDate,
            'payment_type' => $paymentType,
            'description' => $this->generateDescription($paymentType),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'payment_method' => $this->faker->randomElement(EmployeePayment::getPaymentMethods()),
            'reference_number' => $this->faker->optional(0.7)->bothify('REF#####'),
            'status' => $this->faker->randomElement(EmployeePayment::getStatuses()),
            'hours_worked' => $this->faker->optional(0.6)->randomFloat(2, 10, 80),
            'hourly_rate' => $this->faker->optional(0.6)->randomFloat(2, 15, 50),
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    private function generateDescription(string $paymentType): string
    {
        return match($paymentType) {
            'salary' => 'Monthly salary payment',
            'allowance' => $this->faker->randomElement(['Transport allowance', 'Housing allowance', 'Meal allowance']),
            'bonus' => $this->faker->randomElement(['Performance bonus', 'Year-end bonus', 'Project completion bonus']),
            'overtime' => 'Overtime payment',
            'advance' => 'Salary advance',
            'other' => $this->faker->sentence(4),
        };
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmployeePayment::STATUS_COMPLETED,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmployeePayment::STATUS_PENDING,
        ]);
    }
}