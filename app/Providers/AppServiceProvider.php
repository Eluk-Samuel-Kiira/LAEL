<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Setting;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\UnitOfMeasure;
use App\Models\ProductVariant;
use App\Models\Currency;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $variants = ProductVariant::where('is_active', 1)
            ->whereNotIn('id', function ($query) {
                $query->select('variant_id')->from('inventory_items');
            })
            ->get();

        $sub_categories = ProductCategory::where('is_active', 1)->get();
        $uoms = UnitOfMeasure::where('isActive', 1)->get();
        $currencies = Currency::where('isActive', 1)->get();
        $categories = Category::where('is_active', 1)->get();
        $departments = Department::where('isActive', 1)->get();
        $users = User::where('status', 'active')->get();
        $roles = Role::with('permissions')->latest()->get();
        $permissions = Permission::all();
        
        View::share([
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
            'departments' => $departments,
            'categories' => $categories,
            'sub_categories' => $sub_categories,
            'uoms' => $uoms,
            'variants' => $variants,
            'currencies' => $currencies,
        ]);

        $app_mails = Setting::first();
        if ($app_mails) {
            $data =  [
                'transport' => $app_mails->mail_mailer,
                'host' => $app_mails->mail_host,
                'port' => $app_mails->mail_port,
                'username' => $app_mails->mail_username,
                'password' => $app_mails->mail_password,
                'encryption' => $app_mails->mail_encryption,
                'timeout' => null, 
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
                'from' => [
                    'address' => $app_mails->mail_address,
                    'name' => $app_mails->mail_name,
                ],
            ];
            \Config::set('mail.mailers.smtp', $data);
            \Config::set('mail.default', $data['transport']);
            \Config::set('mail.from', $data['from']); 
            
            \Config::set('app.name', $app_mails->app_name); 
        } else {
            \Config::set('mail.mailers.smtp', []);
            \Config::set('mail.from', []);
        }
    }
}
