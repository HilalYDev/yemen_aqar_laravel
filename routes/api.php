<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\OfficeDetailController;
use App\Http\Controllers\Api\PropertyTypeController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PropertyCategoryController;

// Routes للمستخدمين
Route::prefix('users')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verifyPhone', [AuthController::class, 'verifyPhone']);
Route::post('/checkPhone', [ForgotPasswordController::class, 'checkPhone']);
Route::post('/resetPassword', [ForgotPasswordController::class, 'resetPassword']);
Route::post('/resendVerificationCode', [AuthController::class, 'resendVerificationCode']);
Route::post('/getUserData', [AuthController::class, 'getUserData']);



});

// Routes المكاتب العقارية
Route::prefix('officeDetail')->group(function () {
    Route::get('/index', [OfficeDetailController::class, 'index']);
    Route::post('/show', [OfficeDetailController::class, 'show']);
    Route::post('/officeProperties', [OfficeDetailController::class, 'getOfficeProperties']);

    Route::post('/store', [OfficeDetailController::class, 'store']);
    Route::post('/update', [OfficeDetailController::class, 'update']);
    Route::post('/destroy', [OfficeDetailController::class, 'destroy']);
});
// Routes لفئات العقارات
Route::prefix('property-categories')->group(function () {
    Route::get('/index', [PropertyCategoryController::class, 'index']);
    Route::post('/show', [PropertyCategoryController::class, 'show']);

    Route::post('/store', [PropertyCategoryController::class, 'store']);
    Route::post('/update', [PropertyCategoryController::class, 'update']);
    Route::post('/destroy', [PropertyCategoryController::class, 'destroy']);
});

// Routes لأنواع العقارات
Route::prefix('property-types')->group(function () {
    Route::get('/index', [PropertyTypeController::class, 'index']);
    Route::post('/show', [PropertyTypeController::class, 'show']);
    Route::post('/store', [PropertyTypeController::class, 'store']);
    Route::post('/update', [PropertyTypeController::class, 'update']);
    Route::post('/destroy', [PropertyTypeController::class, 'destroy']);
});

// Routes للعقارات
Route::prefix('properties')->group(function () {
    Route::get('/index', [PropertyController::class, 'index']);
    Route::post('/show', [PropertyController::class, 'show']);
    Route::post('/store', [PropertyController::class, 'store']);
    Route::post('/update', [PropertyController::class, 'update']);
    Route::post('/destroy', [PropertyController::class, 'destroy']);
});
// Routes للبحث
Route::prefix('search')->group(function () {
    Route::post('/search', [SearchController::class, 'searchProperty']);
    Route::post('/show', [SearchController::class, 'show']);
    Route::post('/store', [SearchController::class, 'store']);
    Route::post('/update', [SearchController::class, 'update']);
    Route::post('/destroy', [SearchController::class, 'destroy']);
});
// Routes السلة
Route::prefix('cart')->group(function () {
    Route::get('/index', [CartController::class, 'index']);
    Route::post('/show', [CartController::class, 'show']);
    Route::post('/store', [CartController::class, 'store']);
    Route::post('/update', [CartController::class, 'update']);
    Route::post('/destroy', [CartController::class, 'destroy']);
});
// Routes السلة
Route::prefix('orders')->group(function () {
     Route::get('/index', [OrderController::class, 'index']);
    Route::post('/checkout', [OrderController::class, 'checkout']);
       

    Route::post('/show', [OrderController::class, 'show']);
    Route::post('/userOrders', [OrderController::class, 'userOrders']);
    // Route::post('/update', [OrderController::class, 'update']);
    // Route::post('/destroy', [OrderController::class, 'destroy']);
});

Route::prefix('order-item')->group(function () {
     Route::get('/index', [OrderItemController::class, 'index']);
       

    Route::post('/show', [OrderItemController::class, 'show']);
    // Route::post('/store', [OrderController::class, 'store']);
    // Route::post('/update', [OrderController::class, 'update']);
    // Route::post('/destroy', [OrderController::class, 'destroy']);
});