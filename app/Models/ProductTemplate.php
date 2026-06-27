<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTemplate extends Model
{
    protected $fillable = [
        'product_id', 'name', 'preview_image',
        'fabric_json', 'canva_template_url', 'canva_template_id',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'fabric_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }

    public function hasCanva(): bool
    {
        return (bool) $this->canva_template_url;
    }
}
