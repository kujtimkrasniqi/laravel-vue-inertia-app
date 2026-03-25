<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Centralises all reusable Client query logic so controllers stay thin
 * and the same filter/stats/format rules are never duplicated.
 */
class ClientQueryService
{
    /** Valid filter keys accepted from query-string / export requests. */
    public const FILTERS = ['all', 'active', 'expired', 'this_week', 'this_month'];

    // -------------------------------------------------------------------------
    // Stats
    // -------------------------------------------------------------------------

    /**
     * Return aggregate counts for the dashboard and filter bar.
     * Uses COUNT queries — never loads a full collection into memory.
     */
    public function stats(): array
    {
        $today    = Carbon::today();
        $weekEnd  = Carbon::today()->endOfWeek();
        $monthEnd = Carbon::today()->endOfMonth();

        return [
            'total'      => Client::count(),
            'active'     => Client::where('expiry_date', '>=', $today)->count(),
            'expired'    => Client::where('expiry_date', '<',  $today)->count(),
            'this_week'  => Client::whereBetween('expiry_date', [$today, $weekEnd])->count(),
            'this_month' => Client::whereBetween('expiry_date', [$today, $monthEnd])->count(),
        ];
    }

    // -------------------------------------------------------------------------
    // Filtered collection
    // -------------------------------------------------------------------------

    /**
     * Return a mapped collection of clients, optionally filtered.
     *
     * @param  string|null  $filter  One of self::FILTERS, or null / 'all' for no filter.
     */
    public function filteredList(?string $filter): Collection
    {
        $today    = Carbon::today();
        $weekEnd  = Carbon::today()->endOfWeek();
        $monthEnd = Carbon::today()->endOfMonth();

        return Client::query()
            ->when($filter === 'active',     fn ($q) => $q->where('expiry_date', '>=', $today))
            ->when($filter === 'expired',    fn ($q) => $q->where('expiry_date', '<',  $today))
            ->when($filter === 'this_week',  fn ($q) => $q->whereBetween('expiry_date', [$today, $weekEnd]))
            ->when($filter === 'this_month', fn ($q) => $q->whereBetween('expiry_date', [$today, $monthEnd]))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Client $c) => self::format($c));
    }

    // -------------------------------------------------------------------------
    // Formatting
    // -------------------------------------------------------------------------

    /**
     * Map a Client model to a plain, JSON-safe array for Inertia props.
     * Single source of truth — used by controllers, dashboard, and export.
     */
    public static function format(Client $client): array
    {
        return [
            'id'             => $client->id,
            'name'           => $client->name,
            'phone'          => $client->phone,
            'email'          => $client->email,
            'start_date'     => $client->start_date->toDateString(),
            'expiry_date'    => $client->expiry_date->toDateString(),
            'is_active'      => $client->is_active,
            'is_expired'     => $client->is_expired,
            'days_remaining' => $client->daysRemaining(),
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Validate and normalise a raw filter string from the request.
     * Falls back to 'all' for any unknown / missing value.
     */
    public static function resolveFilter(?string $raw): string
    {
        return in_array($raw, self::FILTERS, strict: true) ? $raw : 'all';
    }
}
