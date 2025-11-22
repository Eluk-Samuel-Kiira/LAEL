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
use App\Models\Location;
use App\Models\Customer;
use App\Models\Tax;
use App\Models\{ Promotion, Supplier };
use Illuminate\Support\Facades\Auth;

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
        
        View::composer('*', function ($view) {
            // Initialize with empty collections by default
            $data = [
                'users' => collect(),
                'roles' => collect(),
                'permissions' => collect(),
                'departments' => collect(),
                'categories' => collect(),
                'sub_categories' => collect(),
                'uoms' => collect(),
                'variants' => collect(),
                'currencies' => collect(),
                'locations' => collect(),
                'customers' => collect(),
                'taxes' => collect(),
                'promotions' => collect(),
                'suppliers' => collect(),
            ];

            // Only query if user is authenticated
            if (Auth::check()) {
                $tenantId = Auth::user()->tenant_id;
                
                $data = [

                    'users' => User::where('tenant_id', $tenantId)
                        ->where('status', 'active')
                        ->whereDoesntHave('roles', function ($query) {
                            $query->where('name', 'super_admin');
                        })
                        ->get(),
                    'roles' => Role::where('tenant_id', $tenantId)->with('permissions')->latest()->get(),
                    'permissions' => Permission::regular()->get(),
                    'departments' => Department::where('tenant_id', $tenantId)->where('isActive', 1)->get(),
                    'categories' => Category::where('tenant_id', $tenantId)->where('is_active', 1)->get(),
                    'sub_categories' => ProductCategory::where('tenant_id', $tenantId)->where('is_active', 1)->get(),
                    'uoms' => UnitOfMeasure::where('tenant_id', $tenantId)->where('isActive', 1)->get(),
                    'variants' => ProductVariant::where('tenant_id', $tenantId)->where('is_active', 1)->get(),
                    'currencies' => Currency::where('tenant_id', $tenantId)->where('isActive', 1)->get(),
                    'locations' => Location::where('tenant_id', $tenantId)->where('is_active', 1)->get(),
                    'customers' => Customer::where('tenant_id', $tenantId)->where('is_active', 1)->get(),
                    'taxes' => Tax::where('tenant_id', $tenantId)->where('is_active', 1)->get(),
                    'promotions' => Promotion::where('tenant_id', $tenantId)->where('is_active', 1)->get(),
                    'suppliers' => Supplier::where('tenant_id', $tenantId)->where('is_active', 1)->get(),
                ];
            }

            $view->with($data);
        });

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
