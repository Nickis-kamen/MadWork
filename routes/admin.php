<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => 'admin', 'prefix' => 'admin', 'as' => 'admin.'],function()
{

    Route::get('dashboard', [DashboardController::class, 'index']) -> name('dashboard');
    Route::get('customers', [CustomerController::class, 'index']) -> name('customers');
    Route::get('categories', [CategoryController::class, 'index']) -> name('categories');
    Route::post('categories', [CategoryController::class, 'store']) -> name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update']) -> name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']) -> name('categories.destroy');

});
