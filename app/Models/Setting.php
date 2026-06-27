<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    /** Default values used when a key has never been saved. */
    public const DEFAULTS = [
        'store_name' => 'Elite للدعاية والإعلان',
        'phone' => '0598500721',
        'whatsapp' => '970598500721',
        'email' => 'Eliteagency2024@gmail.com',
        'address' => 'الخليل — الحاووز الأول',
        'working_hours' => 'السبت – الخميس · 9ص – 7م',
        'delivery_individual' => '5–7 أيام عمل',
        'delivery_company' => '3–5 أيام عمل',
        'instagram' => '',
        'facebook' => '',
        'tiktok' => '',
        'store_open' => '1',
        'announcement' => '',
    ];

    /** Get all settings merged over defaults. */
    public static function values(): array
    {
        $stored = Cache::rememberForever('settings.all', function () {
            return static::query()->pluck('value', 'key')->toArray();
        });

        return array_merge(self::DEFAULTS, $stored);
    }

    public static function get(string $key, $default = null)
    {
        return static::values()[$key] ?? $default;
    }

    /** Persist a batch of settings and bust the cache. */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget('settings.all');
    }
}
