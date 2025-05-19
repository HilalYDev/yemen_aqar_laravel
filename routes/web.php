<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('dashboard');
});
// Route::get('/', function () {
//     return view('dashboard');
// })->name('dashboard');

Route::prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/offices', [UserController::class, 'offices'])->name('admin.offices.index');
});
// Route::post('/users/toggle-approval/{id}', [UserController::class, 'toggleApproval'])->name('users.toggle-approval');

// Route::post('/users/toggle-approval/{id}', [UserController::class, 'toggleApproval'])->name('users.toggle-approval');
Route::post('/users/toggle-approval/{id}', [UserController::class, 'toggleApproval'])->name('users.toggle-approval');
Route::post('/users/renew-subscription/{id}', [UserController::class, 'renewSubscription'])
    ->name('users.renew-subscription');