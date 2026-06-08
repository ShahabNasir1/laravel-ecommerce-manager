<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\frontend\dashboardController;
use App\Http\Controllers\brands;
use App\Http\Controllers\categories;
use App\Http\Controllers\colors;
use App\Http\Controllers\sizes;
use App\Http\Controllers\products;

/*
|--------------------------------------------------------------------------
| 1. Public / Guest Routes (Handled by Custom Auth Middleware)
|--------------------------------------------------------------------------
*/

Route::get('/login', [dashboardController::class, 'login'])->name('login');
Route::get('/register', [dashboardController::class, 'register'])->name('register');

// Process Login Form Submission (POST request)
Route::post('/login', [dashboardController::class, 'loginSubmit'])->name('login.submit');
Route::post('/register', [dashboardController::class, 'registerSubmit'])->name('register.submit');
// Route::middleware(['customauth'])->group(function () {

// });

/*
|--------------------------------------------------------------------------
| 2. Protected Routes (Handled by Custom Auth Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['customauth'])->group(function () {

    // Main Dashboard View
    Route::get('/', [dashboardController::class, 'index'])->name('index');

    // Application Resource Routes
    Route::resource('brands', brands::class);
    Route::resource('categories', categories::class);
    Route::resource('colors', colors::class);
    Route::resource('sizes', sizes::class);
    Route::resource('products', products::class);

    // Logout Route
    Route::post('/logout', [dashboardController::class, 'logout'])->name('logout');
});
