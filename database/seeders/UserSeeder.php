<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $roles = [
            'admin',
            'manager',
            'cashier',
            'inventory_clerk',
        ];

        // Create roles
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Define permissions
        $permissions = [
            // Product Management
            'create product',
            'view product',
            'edit product',
            'delete product',
            'update product',

            // Sales Management
            'process sale',
            'view sales report',
            'refund sale',
            'apply discounts',

            // Inventory Management
            'create inventory record',
            'view inventory',
            'edit inventory',
            'delete inventory',
            'update stock levels',

            // Customer Management
            'create customer',
            'view customer',
            'edit customer',
            'delete customer',

            // User Management
            'create user',
            'view user',
            'edit user',
            'delete user',

            // Admin Permissions
            'configure system',
            'manage roles',
            'manage permissions',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();

        $allPermissions = Permission::all();
        $adminRole->syncPermissions($allPermissions); // Admin gets all permissions


        // Create sample users
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'first_name' => 'Samu',
            'last_name' => 'Edu',
            'telephone_number' => '0700000001',
            'job_title' => 'System Administrator',
            'department_id' => 1,
            'profile_image' => '',
            'role_id' => 1,
            'status' => 'active',
            'email' => 'samuelkiiraeluk@gmail.com',
            'password' => bcrypt('1234567890')
        ]);
        $adminUser->assignRole('admin');

    }
}
