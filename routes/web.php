<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Clients — CRUD + markAsPaid
Route::prefix('clients')->name('clients.')->group(function () {
    Route::get('/',                        [ClientController::class, 'index'])->name('index');
    Route::get('/export',                  [ClientController::class, 'export'])->name('export');
    Route::post('/',                       [ClientController::class, 'store'])->name('store');
    Route::put('/{client}',                [ClientController::class, 'update'])->name('update');
    Route::delete('/{client}',             [ClientController::class, 'destroy'])->name('destroy');
    Route::patch('/{client}/mark-as-paid', [ClientController::class, 'markAsPaid'])->name('markAsPaid');
});
