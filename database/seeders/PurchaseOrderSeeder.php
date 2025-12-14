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
        // Get Faker instance
        $faker = \Faker\Factory::create();
        
        // Create 20 purchase orders using DB facade (bypasses Eloquent mutators)
        for ($i = 0; $i < 20; $i++) {
            $subtotal = $faker->randomFloat(2, 100, 10000);
            $taxTotal = $subtotal * 0.1;
            $total = $subtotal + $taxTotal;
            
            $purchaseOrderId = DB::table('purchase_orders')->insertGetId([
                'tenant_id' => \App\Models\Tenant::inRandomOrder()->first()->id ?? 1,
                'supplier_id' => \App\Models\Supplier::inRandomOrder()->first()->id ?? 1,
                'location_id' => \App\Models\Location::inRandomOrder()->first()->id ?? 1,
                'po_number' => 'PO-' . $faker->unique()->numerify('#####'),
                'status' => $faker->randomElement(['draft', 'sent', 'partially_received', 'received', 'cancelled']),
                'expected_delivery_date' => $faker->dateTimeBetween('+1 week', '+1 month'),
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'total' => $total,
                'notes' => $faker->optional()->paragraph(),
                'created_by' => \App\Models\User::inRandomOrder()->first()->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
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

                DB::table('purchase_order_items')->insert([
                    'purchase_order_id' => $purchaseOrderId,
                    'product_variant_id' => $productVariant->id,
                    'product_name' => $productVariant->name,
                    'sku' => $productVariant->sku,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'tax_amount' => $taxAmount,
                    'total_cost' => $totalCost,
                    'received_quantity' => rand(0, $quantity),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}