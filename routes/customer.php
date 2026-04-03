<?php
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\DevisController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => 'customer'],function()
{
    Route::post('devis', [DevisController::class, 'store']) -> name('devis.store');
    Route::get('my-devis', [DevisController::class, 'index']) -> name('my-devis');
    Route::get('devis', [DevisController::class, 'create']) -> name('devis');
});
