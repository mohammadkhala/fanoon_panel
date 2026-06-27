<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $table = 'contact_us';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Scope for unread messages (read_at is null).
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
