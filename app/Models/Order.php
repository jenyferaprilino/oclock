<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'checkout_key',
        'recipient_name',
        'recipient_phone',
        'province_id',
        'city_id',
        'province_name',
        'city_name',
        'district_name',
        'shipping_address',
        'postal_code',
        'notes',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'payment_status',
        'order_status',
        'stock_reservation_status',
        'expires_at',
        'paid_at',
        'courier_name',
        'tracking_number',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'shipping_cost' => 'integer',
            'total_amount' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isAwaitingPayment(): bool
    {
        return $this->order_status === 'awaiting_payment';
    }

    public function canBeProcessed(): bool
    {
        return $this->isPaid() && $this->order_status === 'processing';
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function activePayment()
    {
        return $this->payments()->where('internal_status', 'pending')->latest()->first();
    }
}
