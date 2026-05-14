<?php
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\DevisController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\PaymentController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => 'customer'],function()
{
    Route::post('devis', [DevisController::class, 'store']) -> name('devis.store');
    Route::get('my-devis', [DevisController::class, 'index']) -> name('my-devis');
    Route::get('devis', [DevisController::class, 'create']) -> name('devis');
    Route::post('devis/{devis}/accept', [DevisController::class, 'accept']) -> name('devis.accept');
    // Route::get('devis/{devis}/refuse', [DevisController::class, 'refuse']) -> name('devis.refuse');

    Route::get('payment/{transaction}', [PaymentController::class, 'index']) -> name('payment.index');
    Route::post('payment/{transaction}/first', [PaymentController::class, 'payFirst']) -> name('payment.first');
    Route::post('payment/{transaction}/final', [PaymentController::class, 'payFinal']) -> name('payment.final');

    Route::get('orders', [OrderController::class, 'index']) -> name('orders.index');
    Route::post('orders/{order}/final-payment', [OrderController::class, 'finalPayment']) -> name('orders.final-payment');
});
