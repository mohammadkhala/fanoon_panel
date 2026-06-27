<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUserTypeDiscount extends Model
{
    protected $table = 'product_user_type_discounts';

    protected $fillable = [
        'product_id',
        'user_type_id',
        'discount',
        'discount_type',
    ];

    protected $casts = [
        'discount' => 'float',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }
}
