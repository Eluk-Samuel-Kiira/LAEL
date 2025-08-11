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
