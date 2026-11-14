<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'snap_token',
        'snap_redirect_url',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'internal_status',
        'gross_amount',
        'paid_at',
        'failed_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isTerminal(): bool
    {
        return in_array($this->internal_status, ['paid', 'failed', 'expired', 'canceled', 'refunded']);
    }

    public function isPending(): bool
    {
        return $this->internal_status === 'pending';
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(PaymentNotification::class);
    }
}
