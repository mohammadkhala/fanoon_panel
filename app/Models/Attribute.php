<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Attribute extends Model
{
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function getNameAttribute($name)
    {
        $value = $name;
        if (auth('admin')->check() || auth('branch')->check()) {
            if ((string) $value === '') {
                $value = $this->translations->first()?->value ?? $name;
            }
            return $value;
        }
        return $this->translations[0]->value ?? $name;
    }

    protected static function booted(): void
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}
