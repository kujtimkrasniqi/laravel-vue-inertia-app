<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
    // Accessors (virtual attributes via Laravel Attribute casting)
    // -------------------------------------------------------------------------

    /**
     * is_active: true when expiry_date >= today.
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::today()->lte($this->expiry_date),
        );
    }

    /**
     * is_expired: true when expiry_date < today.
     */
    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::today()->gt($this->expiry_date),
        );
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    /**
     * Mark the client as paid.
     *
     * Extends expiry from the current expiry_date (not today),
     * so renewals always stack correctly regardless of when payment is made.
     *
     * Before: expiry_date = 2026-04-25
     * After:  expiry_date = 2026-05-25
     */
    public function markAsPaid(): void
    {
        $this->expiry_date = $this->expiry_date->addMonth();
        $this->save();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Number of days remaining until expiry (0 if already expired).
     */
    public function daysRemaining(): int
    {
        $diff = Carbon::today()->diffInDays($this->expiry_date, false);

        return max(0, (int) $diff);
    }
}
