<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ClientQueryService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ClientQueryService $query,
    ) {}

    public function __invoke(): Response
    {
        // Recent 5 clients for the mini-table (uses shared format helper)
        $recent = Client::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn (Client $c) => ClientQueryService::format($c));

        return Inertia::render('Dashboard', [
            'stats'  => $this->query->stats(),
            'recent' => $recent,
        ]);
    }
}
