<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCompany extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'api_url',
        'api_key',
        'api_secret',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function orderShipments()
    {
        return $this->hasMany(OrderShipment::class, 'shipping_company_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
