<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientQueryService $query,
    ) {}

    // -------------------------------------------------------------------------
    // GET /clients?filter=active
    // -------------------------------------------------------------------------

    public function index(Request $request): Response
    {
        $filter = ClientQueryService::resolveFilter($request->query('filter'));

        return Inertia::render('Clients/Index', [
            'clients'      => $this->query->filteredList($filter === 'all' ? null : $filter),
            'activeFilter' => $filter,
            'stats'        => $this->query->stats(),
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
    //
    // Extends expiry_date by +1 month from the CURRENT expiry_date — not today.
    // This ensures renewals stack correctly even when paid early or late:
    //   - Paid early  → no days lost, full new month from original expiry
    //   - Paid late   → no free days gained, picks up from where it expired
    // -------------------------------------------------------------------------

    public function markAsPaid(Client $client): RedirectResponse
    {
        $client->markAsPaid();

        return redirect()
            ->route('clients.index')
            ->with('success', "Payment recorded. New expiry: {$client->expiry_date->toDateString()}.");
    }

    // -------------------------------------------------------------------------
    // GET /clients/export?filter=active
    // -------------------------------------------------------------------------

    public function export(Request $request): BinaryFileResponse
    {
        $filter   = ClientQueryService::resolveFilter($request->query('filter'));
        $suffix   = $filter !== 'all' ? "-{$filter}" : '';
        $filename = 'clients' . $suffix . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new ClientsExport($filter !== 'all' ? $filter : null),
            $filename,
        );
    }
}
