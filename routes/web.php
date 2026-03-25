<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dashboard
Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

// Clients — CRUD + markAsPaid
Route::prefix('clients')->name('clients.')->group(function () {
    Route::get('/',                         [ClientController::class, 'index'])->name('index');
    Route::post('/',                        [ClientController::class, 'store'])->name('store');
    Route::put('/{client}',                 [ClientController::class, 'update'])->name('update');
    Route::delete('/{client}',              [ClientController::class, 'destroy'])->name('destroy');
    Route::patch('/{client}/mark-as-paid',  [ClientController::class, 'markAsPaid'])->name('markAsPaid');
});
