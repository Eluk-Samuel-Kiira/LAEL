<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            TenantSeeder::class,
            UserSeeder::class, 
            LocationSeeder::class,
            DepartmentSeeder::class,
            SettingSeeder::class,
            UnitOfMeasureSeeder::class,
            CurrencySeeder::class,
            CategorySeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            InventoryItemSeeder::class,
            InventoryTransactionSeeder::class,
            InventoryAdjustmentsSeeder::class,
            CustomerGroupSeeder::class,
            CustomerSeeder::class,
            OrderSeeder::class,
            OrderTaxSeeder::class,
            OrderPaymentSeeder::class,
        ]);
    }
}
