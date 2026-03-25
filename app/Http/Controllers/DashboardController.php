<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $today    = Carbon::today();
        $weekEnd  = Carbon::today()->endOfWeek();
        $monthEnd = Carbon::today()->endOfMonth();

        // ── Aggregate stats (single query per count, no collection loading) ──

        $stats = [
            'total'       => Client::count(),
            'active'      => Client::where('expiry_date', '>=', $today)->count(),
            'expired'     => Client::where('expiry_date', '<',  $today)->count(),
            'this_week'   => Client::whereBetween('expiry_date', [$today, $weekEnd])->count(),
            'this_month'  => Client::whereBetween('expiry_date', [$today, $monthEnd])->count(),
        ];

        // ── Recent clients (latest 5 for the mini-table) ─────────────────────

        $recent = Client::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn (Client $client) => [
                'id'             => $client->id,
                'name'           => $client->name,
                'phone'          => $client->phone,
                'email'          => $client->email,
                'expiry_date'    => $client->expiry_date->toDateString(),
                'is_active'      => $client->is_active,
                'is_expired'     => $client->is_expired,
                'days_remaining' => $client->daysRemaining(),
            ]);

        return Inertia::render('Dashboard', [
            'stats'  => $stats,
            'recent' => $recent,
        ]);
    }
}
