<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'area_id',
        'name',
        'name_ar',
        'names',
        'sort_order',
    ];

    protected $casts = [
        'area_id' => 'integer',
        'sort_order' => 'integer',
        'names' => 'array',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }

    /**
     * Get city name for a given language code.
     * Reads from names JSON (dynamic languages), then falls back to name / name_ar.
     */
    public function getNameByLang(string $lang): ?string
    {
        $names = $this->names;
        if (is_array($names) && isset($names[$lang]) && (string) $names[$lang] !== '') {
            return (string) $names[$lang];
        }
        return $this->name ?? $this->name_ar ?? null;
    }
}
