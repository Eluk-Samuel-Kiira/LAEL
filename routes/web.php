<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Home\DashboardController;
use App\Http\Controllers\Home\SettingsController;
use Illuminate\Support\Facades\Route;

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

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/change-locale/{locale}', [SettingsController::class, 'switchLocale'])->name('switch.locale');
    Route::put('/settings-update', [SettingsController::class, 'updateSettings'])->name('setting.update');
    Route::post('/logo-upload', [SettingsController::class, 'uploadLogo'])->name('logo.upload');
    Route::post('/favicon-upload', [SettingsController::class, 'uploadFavicon'])->name('favicon.upload');




});

require __DIR__.'/auth.php';
