<?php

use App\Http\Controllers\ClientController;
use App\Models\Client;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dashboard — pass client summary for cards + recent list
Route::get('/', function () {
    $clients = Client::query()
        ->orderByDesc('created_at')
        ->get()
        ->map(fn (Client $client) => [
            'id'             => $client->id,
            'name'           => $client->name,
            'phone'          => $client->phone,
            'email'          => $client->email,
            'start_date'     => $client->start_date->toDateString(),
            'expiry_date'    => $client->expiry_date->toDateString(),
            'is_active'      => $client->is_active,
            'is_expired'     => $client->is_expired,
            'days_remaining' => $client->daysRemaining(),
        ]);

    return Inertia::render('Dashboard', [
        'clients' => $clients,
    ]);
})->name('dashboard');

// Clients — CRUD + markAsPaid
Route::prefix('clients')->name('clients.')->group(function () {
    Route::get('/',                        [ClientController::class, 'index'])->name('index');
    Route::post('/',                       [ClientController::class, 'store'])->name('store');
    Route::put('/{client}',                [ClientController::class, 'update'])->name('update');
    Route::delete('/{client}',             [ClientController::class, 'destroy'])->name('destroy');
    Route::patch('/{client}/mark-as-paid', [ClientController::class, 'markAsPaid'])->name('markAsPaid');
});
