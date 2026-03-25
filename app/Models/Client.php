<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Client model.
 *
 * Lifecycle note:
 *   expiry_date is auto-set by ClientObserver::creating() to start_date + 1 month
 *   when no explicit expiry_date is supplied on creation.
 *
 * Payment note:
 *   markAsPaid() always extends from expiry_date — never from today.
 *   This guarantees renewals stack correctly whether payment is early or late.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $phone
 * @property string|null $email
 * @property Carbon      $start_date
 * @property Carbon      $expiry_date
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 *
 * @property-read bool $is_active
 * @property-read bool $is_expired
 */
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
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * True when expiry_date >= today (subscription is still valid).
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::today()->lte($this->expiry_date),
        );
    }

    /**
     * True when expiry_date < today (subscription has lapsed).
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
     * Record a payment by extending the subscription by one calendar month.
     *
     * Design decision — extend from expiry_date, not today:
     *
     *   Scenario A — client pays 5 days BEFORE expiry:
     *     expiry_date = Apr 25  →  new expiry = May 25  (full month preserved)
     *
     *   Scenario B — client pays 5 days AFTER expiry:
     *     expiry_date = Apr 25  →  new expiry = May 25  (no free days granted)
     *
     *   Scenario C — pay twice in a row (two months at once):
     *     call markAsPaid() → May 25
     *     call markAsPaid() → Jun 25  (stacks correctly)
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
     * Days remaining until expiry. Returns 0 if the subscription has expired.
     */
    public function daysRemaining(): int
    {
        $diff = Carbon::today()->diffInDays($this->expiry_date, false);

        return max(0, (int) $diff);
    }
}
