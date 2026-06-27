<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Design extends Model
{
    protected $fillable = [
        'user_id', 'template_id', 'product_template_id', 'product_id', 'name', 'fabric_json', 'preview_path',
    ];

    protected $casts = [
        'fabric_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function productTemplate(): BelongsTo
    {
        return $this->belongsTo(ProductTemplate::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
