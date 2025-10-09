<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseOrderSeeder extends Seeder
{
    public function run()
    {
        // Reduce the number of purchase orders
        $purchaseOrders = PurchaseOrder::factory()->count(20)->create();

        // Get Faker instance
        $faker = \Faker\Factory::create();

        $purchaseOrders->each(function ($purchaseOrder) use ($faker) {
            // Get random product variants (1-5 items per PO)
            $productVariants = ProductVariant::inRandomOrder()
                ->limit(rand(1, 5))
                ->get();

            // If no product variants exist, create some first
            if ($productVariants->isEmpty()) {
                $productVariants = \App\Models\ProductVariant::factory()->count(5)->create();
            }

            foreach ($productVariants as $productVariant) {
                $quantity = rand(1, 25);
                $unitCost = $faker->randomFloat(2, 5, 50);
                $taxAmount = ($quantity * $unitCost) * 0.1;
                $totalCost = ($quantity * $unitCost) + $taxAmount;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_variant_id' => $productVariant->id,
                    'product_name' => $productVariant->name,
                    'sku' => $productVariant->sku,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'tax_amount' => $taxAmount,
                    'total_cost' => $totalCost,
                    'received_quantity' => rand(0, $quantity),
                ]);
            }
        });
    }
}