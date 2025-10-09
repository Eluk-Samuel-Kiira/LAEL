<?php

use App\Http\Controllers\{ ProfileController, UserController, RoleController};
use App\Http\Controllers\Home\{ DashboardController, LocationController, SettingsController,  UnitOfMeasureController, CurrencyController};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\DepartmentController;
use App\Http\Controllers\Catalog\ {CategoryController, InventoryItemController, ProductVariantController, InventoryAdjustmentsController, ProductController, ProductCategoryController};
use App\Http\Controllers\Orders\{ OrderController, POSController};
use App\Http\Controllers\Setting\{ TaxController,PromotionController };
use App\Http\Controllers\Procurement\{ SupplierController };

    Route::get('/', function () {
        return view('welcome');
    });

    // Error Pages
    Route::get('/error-404', function () {
        return response()->view('layouts.error-404', [], 404); 
    });

    Route::get('/error-500', function () {
        return response()->view('layouts.error-500', [], 500); 
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
    Route::put('/employees/{id}/departments', [UserController::class, 'updateDepartments'])
        ->name('employees.updateDepartments');


    // Roles n Permissions
    Route::get('/role-index', [RoleController::class, 'index'])->name('role.index');
    Route::get('/permission-index', [RoleController::class, 'permissionIndex'])->name('permission.index');
    Route::post('/role-new', [RoleController::class, 'storeRole'])->name('role.store');
    Route::put('/role-update/{role}', [RoleController::class, 'updateRole'])->name('role.update');
    Route::delete('/role-destroy/{id}', [RoleController::class, 'destroy'])->name('role.destroy');
    Route::put('/update-permissions/{id}', [RoleController::class, 'updatePermission'])->name('permission.update');
    Route::put('/revoke-permissions/{id}', [RoleController::class, 'revokePermission'])->name('permission.revoke');

    // Inventory - Category
    Route::resource('category', CategoryController::class);
    Route::post('/category-status/{id}', [CategoryController::class, 'changeCategoryStatus'])->name('category.status');


    Route::resource('product-category', ProductCategoryController::class);
    Route::post('/product-category-status/{id}', [ProductCategoryController::class, 'changeProductCategoryStatus'])->name('product.category.status');

    // Products
    
    Route::resource('products', ProductController::class);
    Route::post('/product-status/{id}', [ProductController::class, 'changeProductStatus'])->name('product.status');
    Route::post('/product-tax-status/{id}', [ProductController::class, 'changeProductTaxStatus'])->name('product.taxable');
    Route::resource('variants', ProductVariantController::class);
    Route::post('/variant-status/{id}', [ProductVariantController::class, 'changeVariantStatus'])->name('variant.status');
    Route::post('/variant-tax-status/{id}', [ProductVariantController::class, 'changeProductVariantTaxStatus'])->name('product.taxable');
    Route::post('/product/upload-image', [ProductController::class, 'uploadProductImage'])->name('product.upload_image');
    Route::post('/variant/upload-image', [ProductVariantController::class, 'uploadVariantImage'])->name('variant.upload_image');
    Route::put('/products/{product}/update-assignment', [ProductController::class, 'updateProductAssignments'])
        ->name('assign.product');
    Route::put('/assignment/{product}', [ProductVariantController::class, 'updateVariantAssignments'])
        ->name('assign.variant');
        

    // Stores and Inventory
    Route::resource('items', InventoryItemController::class);
    Route::resource('stocks', InventoryAdjustmentsController::class);
    Route::put('/transfer-stock/{id}', [InventoryAdjustmentsController::class, 'transferStock'])
        ->name('transfer.stock');

    
    // Location
    Route::resource('locations', LocationController::class);
    Route::post('/location-primary/{id}', [LocationController::class, 'updatePrimaryStatus']);
    Route::post('/location-status/{id}', [LocationController::class, 'updateLocationStatus']);


    
    // Orders and Purchases
    Route::resource('orders', OrderController::class);
    Route::get('/pos-index', [POSController::class, 'index'])->name('pos.index');
    Route::post('/orders/process-payment', [POSController::class, 'processPayment'])
        ->name('orders.process-payment');
    Route::post('/orders/checkout', [POSController::class, 'completePayment'])->name('orders.checkout');


    // Taxes and Promotions
    Route::resource('tax', TaxController::class);
    Route::post('/tax-status/{id}', [TaxController::class, 'updateTaxStatus']);
    Route::resource('promotion', PromotionController::class);
    Route::post('/promotion-status/{id}', [PromotionController::class, 'updatePromotionStatus']);


    // Suppliers & Purchasing
    Route::resource('suppliers', SupplierController::class);
    Route::post('/supplier-status/{id}', [SupplierController::class, 'updateSupplierStatus']);
    
});

require __DIR__.'/auth.php';
