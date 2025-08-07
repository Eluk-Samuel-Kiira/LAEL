<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\TenantConfiguration;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tenant::factory()
            ->count(10)
            ->create()
            ->each(function ($tenant) {
                $tenant->configuration()->create(
                    TenantConfiguration::factory()->make()->toArray()
                );
            });
    }

}
