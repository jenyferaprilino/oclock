<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class City extends Model
{
    public $timestamps = false;

    protected $fillable = ['province_id', 'code', 'name', 'type'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function shippingRate(): HasOne
    {
        return $this->hasOne(ShippingRate::class);
    }

    public function getFullNameAttribute(): string
    {
        $prefix = $this->type === 'city' ? 'Kota' : 'Kab.';

        return "{$prefix} {$this->name}";
    }
}
