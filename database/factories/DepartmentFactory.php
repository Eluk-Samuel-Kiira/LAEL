<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array 
    {
        return [
            'name' => fake()->word() .' Department',
            'isActive' => 1,
            'tenant_id' => $this->faker->randomElement([1, 2, 3, 4]),
            'created_by' => User::where('role_id', 1)->where('status', 'active')->get()->random()->id,
            'manager_id' => User::where('role_id', 1)->where('status', 'active')->get()->random()->id,
        ];
    
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['isActive' => 0]);
    }
    
}
