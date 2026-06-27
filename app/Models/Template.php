<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'category', 'preview_image',
        'fabric_json', 'retail_price', 'wholesale_price', 'badge',
        'rating', 'reviews_count', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'fabric_json' => 'array',
        'retail_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'rating' => 'decimal:1',
        'is_active' => 'boolean',
    ];

    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }

    /** Price shown to a given user (wholesale for approved companies). */
    public function priceFor(?User $user): float
    {
        return $user && $user->seesWholesale()
            ? (float) $this->wholesale_price
            : (float) $this->retail_price;
    }
}
