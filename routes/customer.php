<?php
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\DevisController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => 'customer', 'prefix' => 'customer', 'as' => 'customer.'],function()
{
    Route::get('dashboard', [DashboardController::class, 'index']) -> name('dashboard');
    Route::post('devis', [DevisController::class, 'store']) -> name('devis.store');
    Route::get('devis', [DevisController::class, 'index']) -> name('devis');
});
