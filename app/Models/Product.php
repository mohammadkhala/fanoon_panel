<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{

    protected $casts = [
        'tax' => 'float',
        'price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'set_menu' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'wishlist_count' => 'integer',
        'total_stock' => 'integer',
        'minimum_stock_alert' => 'integer',
        'visible_to_user_types' => 'array',  // NULL = everyone; [] = hidden; [id,...] = specific types; 0 = guests
    ];

    protected $appends = ['image_fullpath'];

    /**
     * المنتجات التي انخفض مخزونها عن حد التنبيه.
     */
    public function scopeLowStock($query, ?int $defaultAlert = null)
    {
        $default = $defaultAlert ?? (int) (\App\CentralLogics\Helpers::get_business_settings('default_minimum_stock_alert') ?? 5);
        return $query->where(function ($q) use ($default) {
            $q->whereNotNull('minimum_stock_alert')
                ->whereColumn('total_stock', '<=', 'minimum_stock_alert');
        })->orWhere(function ($q) use ($default) {
            $q->whereNull('minimum_stock_alert')
                ->where('total_stock', '<=', $default)
                ->where('total_stock', '>=', 0);
        });
    }

    public function getImageFullPathAttribute()
    {
        $value = $this->image ?? [];
        $imageUrlArray = is_array($value) ? $value : json_decode($value, true);
        if (is_array($imageUrlArray)) {
            if (empty($imageUrlArray))
            {
                return [asset('assets/admin/img/160x160/img2.jpg')];
            }
            foreach ($imageUrlArray as $key => $item) {
                if (Storage::disk('public')->exists('product/' . $item)) {
                    $imageUrlArray[$key] = asset('storage/product/'. $item) ;
                } else {
                    $imageUrlArray[$key] = asset('assets/admin/img/160x160/img2.jpg');
                }
            }
        }
        return $imageUrlArray;
    }

    public function translations(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class)->latest();
    }

    public function rating()
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, product_id'))
            ->groupBy('product_id');
    }

    public function userTypePrices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductUserTypePrice::class);
    }

    public function userTypeDiscounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductUserTypeDiscount::class);
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag')->withTimestamps();
    }

    public function productRelations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProductRelation::class, 'product_id');
    }

    public function relatedProducts(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'related_product_id')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    protected static function booted()
    {
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function($query){
                return $query->where('locale', app()->getLocale());
            }]);
        });
    }
}
