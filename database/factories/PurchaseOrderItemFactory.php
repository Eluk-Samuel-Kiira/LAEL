<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition()
    {
        // Get a random existing product variant or create one if none exists
        $productVariant = ProductVariant::inRandomOrder()->first() ?? ProductVariant::factory()->create();
        
        $quantity = $this->faker->numberBetween(1, 50);
        $unitCost = $this->faker->randomFloat(2, 1, 100); 
        $taxAmount = ($quantity * $unitCost) * 0.1;
        $totalCost = ($quantity * $unitCost) + $taxAmount;

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_variant_id' => $productVariant->id,
            'product_name' => $productVariant->name, // Use actual product name
            'sku' => $productVariant->sku, // Use actual SKU
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'tax_amount' => $taxAmount,
            'total_cost' => $totalCost,
            'received_quantity' => $this->faker->numberBetween(0, $quantity),
        ];
    }
}