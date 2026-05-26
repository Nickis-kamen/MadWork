<?php

use App\Http\Controllers\Agency\AgencyController;
use App\Http\Controllers\Agency\DashboardController;
use App\Http\Controllers\Agency\DevisController;
use App\Http\Controllers\Agency\OrderController;
use App\Http\Controllers\Agency\PaymentController;
use App\Http\Controllers\Agency\ProjectController;
use App\Http\Controllers\Agency\ServiceController;
use App\Http\Controllers\Agency\SubscriptionController;
use Illuminate\Support\Facades\Route;




Route::group(['middleware' => 'agency', 'prefix' => 'agency', 'as' => 'agency.'],function()
{
    Route::get('dashboard', [DashboardController::class, 'index']) -> name('dashboard');

    Route::post('my-agency', [AgencyController::class, 'store']) -> name('store');
    Route::put('my-agency/{agency}', [AgencyController::class, 'update']) -> name('update');
    Route::delete('my-agency/{agency}', [AgencyController::class, 'destroy']) -> name('destroy');
    Route::get('my-agency', [AgencyController::class, 'index']) -> name('index');

    Route::get('my-services', [ServiceController::class, 'index']) -> name('services.index');
    Route::post('my-services', [ServiceController::class, 'store']) -> name('services.store');
    Route::put('my-services/{service}', [ServiceController::class, 'update']) -> name('services.update');
    Route::delete('my-services/{service}', [ServiceController::class, 'destroy']) -> name('services.destroy');
    Route::post('my-services/{service}/details', [ServiceController::class, 'addDetail']) -> name('services.addDetail');
    Route::delete('my-services/details/{id}', [ServiceController::class, 'removeDetail']) -> name('services.removeDetail');

    Route::get('my-devis', [DevisController::class, 'index']) -> name('devis.index');
    Route::put('my-devis/{devis}/propose', [DevisController::class, 'propose']) -> name('devis.propose');

    Route::get('orders', [OrderController::class, 'index']) -> name('orders.index');
    Route::put('/orders/{id}/progress', [OrderController::class, 'updateProgress'])->name('orders.progress');
    Route::put('/orders/{id}/delivered', [OrderController::class, 'delivered'])->name('orders.delivered');

    Route::post('/plan/{plan}', [SubscriptionController::class, 'plan']) -> name('plan.subscription');
    Route::get('/subscriptions', [SubscriptionController::class, 'index']) -> name('subscriptions.index');

    Route::get('/payment/{transaction}', [PaymentController::class, 'index']) -> name('payments.index');
    Route::post('/payment/{transaction}', [PaymentController::class, 'paySubscription']) -> name('payment.paySubscription');

    Route::post('/project/{id}/proposal', [ProjectController::class, 'submitProposal']) -> name('project.proposal');
});
