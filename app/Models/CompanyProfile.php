<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyProfile extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'trade_license_no', 'trade_license_path',
        'city', 'address', 'status', 'review_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
