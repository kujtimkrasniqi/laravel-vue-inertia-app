<?php

namespace App\Observers;

use App\Models\Client;
use Illuminate\Support\Carbon;

class ClientObserver
{
    /**
     * Auto-set expiry_date = start_date + 1 month when no expiry is provided.
     *
     * This fires before INSERT so the default is always in place, but an
     * explicitly passed expiry_date (e.g. from seeders or overrides) is
     * respected and never overwritten.
     */
    public function creating(Client $client): void
    {
        if (! $client->expiry_date && $client->start_date) {
            $client->expiry_date = Carbon::parse($client->start_date)->addMonth();
        }
    }
}
