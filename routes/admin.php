<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => 'admin', 'prefix' => 'admin', 'as' => 'admin.'],function()
{

    Route::get('dashboard', [DashboardController::class, 'index']) -> name('dashboard');
    Route::get('customers', [CustomerController::class, 'index']) -> name('customers');

});
