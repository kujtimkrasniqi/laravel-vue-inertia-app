<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    // -------------------------------------------------------------------------
    // GET /clients
    // -------------------------------------------------------------------------

    public function index(): Response
    {
        $clients = Client::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Client $client) => [
                'id'           => $client->id,
                'name'         => $client->name,
                'phone'        => $client->phone,
                'email'        => $client->email,
                'start_date'   => $client->start_date->toDateString(),
                'expiry_date'  => $client->expiry_date->toDateString(),
                'is_active'    => $client->is_active,
                'is_expired'   => $client->is_expired,
                'days_remaining' => $client->daysRemaining(),
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /clients
    // -------------------------------------------------------------------------

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    // -------------------------------------------------------------------------
    // PUT /clients/{client}
    // -------------------------------------------------------------------------

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    // -------------------------------------------------------------------------
    // DELETE /clients/{client}
    // -------------------------------------------------------------------------

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // PATCH /clients/{client}/mark-as-paid
    // -------------------------------------------------------------------------

    public function markAsPaid(Client $client): RedirectResponse
    {
        $client->markAsPaid();

        return redirect()
            ->route('clients.index')
            ->with('success', "Payment recorded. New expiry: {$client->expiry_date->toDateString()}.");
    }
}
