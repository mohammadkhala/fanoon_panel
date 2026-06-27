<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = ['url', 'ip', 'referer', 'user_id', 'visited_on'];

    protected $casts = ['visited_on' => 'date'];
}
