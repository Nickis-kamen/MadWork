<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    return Inertia::render('Welcome');
});
Route::get('/test', function () {
    // sleep(2);
    return Inertia::render('Test');
});
