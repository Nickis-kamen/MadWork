<?php

use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DevisController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => 'admin', 'prefix' => 'admin', 'as' => 'admin.'],function()
{

    Route::get('dashboard', [DashboardController::class, 'index']) -> name('dashboard');

    Route::get('users', [UserController::class, 'index']) -> name('users');
    Route::get('agencies', [AgencyController::class, 'index']) -> name('agencies');

    Route::get('categories', [CategoryController::class, 'index']) -> name('categories');
    Route::post('categories', [CategoryController::class, 'store']) -> name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update']) -> name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']) -> name('categories.destroy');

    Route::get('subscriptions', [SubscriptionController::class, 'index']) -> name('subscriptions');
    Route::put('subscriptions/{subscription}/active', [SubscriptionController::class, 'active']) -> name('subscriptions.active');

    Route::get('devis', [DevisController::class, 'index']) -> name('devis');

    Route::get('orders', [OrderController::class, 'index']) -> name('orders');
});
