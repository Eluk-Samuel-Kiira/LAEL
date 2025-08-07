<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Tenant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company;
        $subdomain = strtolower(preg_replace('/\s+/', '', $name)) . '.pointofsale.com';

        return [
            'name' => $name,
            'subdomain' => $subdomain,
            'status' => $this->faker->randomElement(['active', 'suspended', 'trial']),
        ];
    }
}
