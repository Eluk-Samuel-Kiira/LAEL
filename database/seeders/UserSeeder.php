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

            // Product Management
            'create variant',
            'view variant',
            'edit variant',
            'delete variant',
            'update variant',
            

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


            // Admin Permissions
            'configure system',
            'manage roles',
            'manage permissions',

            // Unit of measure Management
            'create uom',
            'view uom',
            'edit uom',
            'delete uom',
            'update uom',
            
            // Department
            'create department',
            'view department',
            'edit department',
            'delete department',
            'update department',

            // Tenant mgt
            'create tenant',
            'view tenant',
            'edit tenant',
            'delete tenant',
            'update tenant',

            // User Management
            'create user',
            'view user',
            'edit user',
            'delete user',
            'update user',
            
            // Category Management
            'create category',
            'view category',
            'edit category',
            'delete category',
            'update category',
            
            // Location Management
            'create location',
            'view location',
            'edit location',
            'delete location',
            'update location',

            // subcategory Management
            'create subcategory',
            'view subcategory',
            'edit subcategory',
            'delete subcategory',
            'update subcategory',

            // tax Management
            'create tax',
            'view tax',
            'edit tax',
            'delete tax',
            'update tax',

            // promotion Management
            'create promotion',
            'view promotion',
            'edit promotion',
            'delete promotion',
            'update promotion',

            // Admin Management
            'admin only',
            'create role',
            'edit role',
            'delete role',
            'update role',
            'update permission',
            'update settings',
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
        User::factory()->count(10)->create();

    }
}
