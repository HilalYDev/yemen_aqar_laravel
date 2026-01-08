<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;

// ✅ تحميل مسارات تسجيل الدخول / التسجيل الجاهزة
require __DIR__.'/auth.php';

// ✅ الصفحة الرئيسية
Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') :view('auth.login');

});

// ✅ لوحة التحكم (محمية بوسيط auth و verified)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



// ✅ مسارات المستخدمين المصادق عليهم
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])  ->name('dashboard');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/owners', [UserController::class, 'propertyOwners'])->name('admin.owners.index');

    // ========================================

    
    Route::post('/users/toggle-approval/{id}', [UserController::class, 'toggleApproval'])
        ->name('users.toggle-approval');
        
    Route::post('/users/renew-subscription/{id}', [UserController::class, 'renewSubscription'])
        ->name('users.renew-subscription');

            Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');

    // =========================

        //     Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        // Route::get('/owners', [UserController::class, 'propertyOwners'])->name('admin.owners.index');
        
        // // ✅ تفعيل/إلغاء تفعيل المستخدم
        // Route::post('/users/{user}/toggle-approval', [UserController::class, 'toggleApproval'])
        //      ->name('users.toggle-approval');
        
        // // ✅ تجديد صلاحية المستخدم
        // Route::post('/users/{user}/renew-subscription', [UserController::class, 'renewSubscription'])
        //      ->name('users.renew-subscription');
        
        // // ✅ عرض تفاصيل المستخدم
        // Route::get('/users/{user}', [UserController::class, 'show'])
        //      ->name('users.show');
    
});
});


