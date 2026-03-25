<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Client extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'start_date',
        'expiry_date',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'expiry_date' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Boot: auto-set expiry_date = start_date + 1 month on create
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Client $client): void {
            if (! $client->expiry_date && $client->start_date) {
                $client->expiry_date = Carbon::parse($client->start_date)
                    ->addMonth();
            }
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Whether the client subscription is currently active.
     */
    public function isActive(): bool
    {
        return Carbon::today()->lte($this->expiry_date);
    }

    /**
     * Number of days remaining until expiry (0 if already expired).
     */
    public function daysRemaining(): int
    {
        $diff = Carbon::today()->diffInDays($this->expiry_date, false);

        return max(0, (int) $diff);
    }
}
