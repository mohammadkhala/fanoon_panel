<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipment extends Model
{
    protected $fillable = [
        'order_id',
        'shipping_company_id',
        'tracking_number',
        'status',
        'notes',
        'shipped_at',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function shippingCompany()
    {
        return $this->belongsTo(ShippingCompany::class, 'shipping_company_id', 'id');
    }
}
