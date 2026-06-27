<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    protected $fillable = ['name', 'url', 'events', 'secret', 'is_active'];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
    ];

    public function subscribesTo(string $event): bool
    {
        return in_array($event, $this->events ?? [], true);
    }
}
