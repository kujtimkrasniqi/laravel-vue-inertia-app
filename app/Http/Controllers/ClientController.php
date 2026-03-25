<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    // -------------------------------------------------------------------------
    // Allowed filter values
    // -------------------------------------------------------------------------

    private const FILTERS = ['all', 'active', 'expired', 'this_week', 'this_month'];

    // -------------------------------------------------------------------------
    // GET /clients?filter=active
    // -------------------------------------------------------------------------

    public function index(Request $request): Response
    {
        $filter = in_array($request->query('filter'), self::FILTERS)
            ? $request->query('filter')
            : 'all';

        $today     = Carbon::today();
        $weekEnd   = Carbon::today()->endOfWeek();
        $monthEnd  = Carbon::today()->endOfMonth();

        $clients = Client::query()
            ->when($filter === 'active',     fn ($q) => $q->where('expiry_date', '>=', $today))
            ->when($filter === 'expired',    fn ($q) => $q->where('expiry_date', '<',  $today))
            ->when($filter === 'this_week',  fn ($q) => $q->whereBetween('expiry_date', [$today, $weekEnd]))
            ->when($filter === 'this_month', fn ($q) => $q->whereBetween('expiry_date', [$today, $monthEnd]))
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

        return Inertia::render('Clients/Index', [
            'clients'       => $clients,
            'activeFilter'  => $filter,
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
