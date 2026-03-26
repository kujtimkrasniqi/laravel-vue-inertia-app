<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ── Guest-only routes (redirect to dashboard if already logged in) ────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
});

Route::post('/login',    [AuthController::class, 'login'])->name('login.submit');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated routes ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', DashboardController::class)->name('dashboard');

    // Clients — CRUD + markAsPaid + export
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/',                        [ClientController::class, 'index'])->name('index');
        Route::get('/export',                  [ClientController::class, 'export'])->name('export');
        Route::post('/',                       [ClientController::class, 'store'])->name('store');
        Route::put('/{client}',                [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}',             [ClientController::class, 'destroy'])->name('destroy');
        Route::patch('/{client}/mark-as-paid', [ClientController::class, 'markAsPaid'])->name('markAsPaid');
    });
});
