<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderStatusLog extends Model
{
    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'changed_by_type',
        'changed_by_id',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function changedBy(): MorphTo
    {
        return $this->morphTo('changed_by', 'changed_by_type', 'changed_by_id');
    }

    /**
     * عرض من غيّر (للـ UI).
     */
    public function getChangedByDisplayAttribute(): string
    {
        if ($this->changed_by_type === 'system' || empty($this->changed_by_type)) {
            return translate('system') ?: 'System';
        }
        if ($this->changedBy) {
            return $this->changedBy->f_name . ' ' . ($this->changedBy->l_name ?? '');
        }
        return translate('unknown') ?: 'Unknown';
    }
}
