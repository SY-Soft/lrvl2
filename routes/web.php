<?php

use App\Http\Controllers\Auth\PortalAuthController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserTicketController;
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

Route::get('/dashboard', UserDashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')
    ->prefix('dashboard/tickets')
    ->name('dashboard.tickets.')
    ->group(function () {
        Route::get('/', [UserTicketController::class, 'index'])->name('index');
        Route::get('/create', [UserTicketController::class, 'create'])->name('create');
        Route::post('/', [UserTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [UserTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/comments', [UserTicketController::class, 'comment'])->name('comments.store');
        Route::patch('/{ticket}/status', [UserTicketController::class, 'status'])->name('status.update');
    });
