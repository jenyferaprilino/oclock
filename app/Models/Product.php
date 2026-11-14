<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'brand',
        'description',
        'watch_type',
        'strap_material',
        'color',
        'case_diameter',
        'movement_type',
        'water_resistance',
        'price',
        'stock',
        'image_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        return $this->is_active && $this->stock > 0;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image_path
            ? asset('storage/'.$this->image_path)
            : asset('images/placeholder-watch.jpg');
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
