<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Home\DashboardController;
use App\Http\Controllers\Home\SettingsController;
use App\Http\Controllers\Home\UnitOfMeasureController;
use App\Http\Controllers\Home\CurrencyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Catalog\CategoryController;
use App\Http\Controllers\Catalog\ProductCategoryController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', [DashboardController::class, 'index'] )->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/overview', [DashboardController::class, 'overview'] )->middleware(['auth', 'verified'])->name('overview');

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage'])->name('profile.upload_image');


    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/change-locale/{locale}', [SettingsController::class, 'switchLocale'])->name('switch.locale');
    Route::put('/settings-update', [SettingsController::class, 'updateSettings'])->name('setting.update');
    Route::post('/logo-upload', [SettingsController::class, 'uploadLogo'])->name('logo.upload');
    Route::post('/favicon-upload', [SettingsController::class, 'uploadFavicon'])->name('favicon.upload');

    // Units of measure
    Route::resource('uom', UnitOfMeasureController::class);
    Route::post('/uom-status/{id}', [UnitOfMeasureController::class, 'changeUOMStatus'])->name('uom.status');

    // Currency
    Route::resource('currency', CurrencyController::class);
    Route::post('/currency-status/{id}', [CurrencyController::class, 'changeCurrencyStatus'])->name('currency.status');

    // Department
    Route::resource('department', DepartmentController::class);
    Route::post('/department-status/{id}', [DepartmentController::class, 'changeDepartmentStatus'])->name('currency.status');


    // Users/Employees
    Route::resource('employee', UserController::class);
    Route::post('/employee-status/{id}', [UserController::class, 'changeEmployeeStatus'])->name('employee.status');


    // Roles n Permissions
    Route::get('/role-index', [RoleController::class, 'index'])->name('role.index');
    Route::get('/permission-index', [RoleController::class, 'permissionIndex'])->name('permission.index');
    Route::post('/role-new', [RoleController::class, 'storeRole'])->name('role.store');
    Route::put('/role-update/{role}', [RoleController::class, 'updateRole'])->name('role.update');
    Route::delete('/role-destroy/{id}', [RoleController::class, 'destroy'])->name('role.destroy');
    Route::put('/update-permissions/{id}', [RoleController::class, 'updatePermission'])->name('permission.update');
    Route::put('/revoke-permissions/{id}', [RoleController::class, 'revokePermission'])->name('permission.revoke');

    // Inventory
    Route::resource('category', CategoryController::class);
    Route::post('/category-status/{id}', [CategoryController::class, 'changeCategoryStatus'])->name('category.status');

    Route::resource('product-category', ProductCategoryController::class);
    Route::post('/product-category-status/{id}', [ProductCategoryController::class, 'changeProductCategoryStatus'])->name('product.category.status');

});

require __DIR__.'/auth.php';
