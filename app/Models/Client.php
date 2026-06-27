<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'link',
        'position',
        'status',
    ];

    protected $casts = [
        'position' => 'integer',
        'status' => 'integer',
    ];

    protected $appends = ['logo_fullpath'];

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function getLogoFullpathAttribute(): ?string
    {
        $logo = $this->logo ?? null;
        if (!is_null($logo) && Storage::disk('public')->exists('client/' . $logo)) {
            return asset('storage/client/' . $logo);
        }
        return null;
    }
}
