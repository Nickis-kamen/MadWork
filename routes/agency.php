<?php

use App\Http\Controllers\Agency\AgencyController;
use App\Http\Controllers\Agency\DashboardController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => 'agency', 'prefix' => 'agency', 'as' => 'agency.'],function()
{
    Route::get('dashboard', [DashboardController::class, 'index']) -> name('dashboard');
    Route::post('my-agency', [AgencyController::class, 'store']) -> name('store');
    Route::put('my-agency/{agency}', [AgencyController::class, 'update']) -> name('update');
    Route::delete('my-agency/{agency}', [AgencyController::class, 'destroy']) -> name('destroy');
    Route::get('my-agency', [AgencyController::class, 'index']) -> name('index');
});
