<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = [
        'address_type',
        'contact_person_name',
        'contact_person_number',
        'address',
        'city',
        'area_id',
        'road',
        'house',
        'floor',
        'longitude',
        'latitude',
        'user_id',
        'is_guest',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_guest' => 'integer',
        'area_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }
}
