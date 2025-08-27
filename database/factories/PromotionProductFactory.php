<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PromotionProduct;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\ProductVariant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PromotionProduct>
 */
class PromotionProductFactory extends Factory
{
    protected $model = PromotionProduct::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'promotion_id' => Promotion::factory(),
            'product_id' => Product::factory(),
            'variant_id' => ProductVariant::factory(),
            'applies_to' => $this->faker->randomElement(['specific_products', 'product_categories', 'all_products']),
        ];
    }
}
