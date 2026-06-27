<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'branch_id',
        'name_en',
        'name_ar',
        'names',
        'delivery_charge',
        'sort_order',
    ];

    protected $casts = [
        'branch_id' => 'integer',
        'delivery_charge' => 'float',
        'sort_order' => 'integer',
        'names' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'area_id', 'id')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get area name for a given language code.
     * Reads from names JSON (dynamic languages), then falls back to name_en / name_ar.
     */
    public function getNameByLang(string $lang): ?string
    {
        $names = $this->names;
        if (is_array($names) && isset($names[$lang])) {
            $translated = trim((string) $names[$lang]);
            if ($translated !== '') {
                return $translated;
            }
        }

        $nameAr = trim((string) ($this->name_ar ?? ''));
        $nameEn = trim((string) ($this->name_en ?? ''));

        if ($lang === 'ar') {
            return $nameAr !== '' ? $nameAr : ($nameEn !== '' ? $nameEn : null);
        }

        return $nameEn !== '' ? $nameEn : ($nameAr !== '' ? $nameAr : null);
    }
}
