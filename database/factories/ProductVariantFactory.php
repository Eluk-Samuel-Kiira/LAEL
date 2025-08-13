<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ProductVariant::class;

    public function definition(): array
    {
        return [
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'name' => $this->faker->word(),
            'barcode' => $this->faker->ean13(),
            'price' => $this->faker->randomFloat(2, 5, 500),
            'cost_price' => $this->faker->randomFloat(2, 3, 400),
            'image_url' => $this->faker->imageUrl(640, 480, 'products', true),
            'weight' => $this->faker->randomFloat(2, 50, 2000),
            'weight_unit' => $this->faker->randomElement([1, 2, 4, 3, 5]),
            'is_active' => $this->faker->boolean(90),
            'tenant_id' => 1,
            'created_by' => User::where('role_id', '1' )->where('status', 'active')->get()->random()->id,
        ];
    }
}
