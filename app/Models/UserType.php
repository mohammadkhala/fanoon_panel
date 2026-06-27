<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class UserType extends Model
{
    protected $fillable = [
        'name',
        'is_default',
        'position',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_type_id');
    }

    public function requestedByUsers(): HasMany
    {
        return $this->hasMany(User::class, 'requested_user_type_id');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductUserTypePrice::class, 'user_type_id');
    }

    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first();
    }
}
