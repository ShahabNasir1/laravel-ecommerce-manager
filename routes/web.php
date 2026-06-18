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
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [dashboardController::class, 'login'])->name('login');
Route::get('/register', [dashboardController::class, 'register'])->name('register');

Route::post('/login', [dashboardController::class, 'loginSubmit'])->name('login.submit');
Route::post('/register', [dashboardController::class, 'registerSubmit'])->name('register.submit');

/*
|--------------------------------------------------------------------------
| Protected Routes (CHANGE THIS LINE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () { // Switched from 'customauth' to 'auth'

    Route::get('/', [dashboardController::class, 'index'])->name('index');

    Route::middleware(['isAdmin'])->group(function () {
        Route::resource('brands', brands::class);
        Route::resource('categories', categories::class);
        Route::resource('colors', colors::class);
        Route::resource('sizes', sizes::class);
    });

    Route::resource('products', products::class);
    Route::post('/logout', [dashboardController::class, 'logout'])->name('logout');
});