<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DesignTemplate extends Model
{
    protected $fillable = [
        'name', 'category_id', 'product_id', 'canvas_json', 'thumbnail',
        'canvas_width', 'canvas_height', 'status', 'position',
    ];

    public function mainCategory()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getThumbnailFullpathAttribute(): ?string
    {
        if ($this->thumbnail && Storage::disk('public')->exists('design-templates/' . $this->thumbnail)) {
            return asset('storage/design-templates/' . $this->thumbnail);
        }
        return null;
    }
}
