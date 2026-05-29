<?php

use App\Http\Controllers\Auth\PortalAuthController;
use Illuminate\Support\Facades\Route;



Route::middleware('guest')->group(function () {
    Route::get('/login', [PortalAuthController::class, 'create'])->name('login');
    Route::post('/login', [PortalAuthController::class, 'store'])->name('login.store');
    Route::get('/auth/google', [PortalAuthController::class, 'google'])->name('login.google');
});

Route::post('/logout', [PortalAuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::view('/', 'pages.home')->name('home');
