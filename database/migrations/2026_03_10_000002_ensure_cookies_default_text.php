<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure cookies text has default values when empty.
     * Updates empty text only. Does not overwrite user customizations.
     */
    public function up(): void
    {
        $defaults = [
            'ar' => 'نستخدم ملفات تعريف الارتباط لتحسين تجربتك على الموقع وتذكر تفضيلاتك. يمكنك تعطيلها من إعدادات المتصفح. بمتابعة التصفح، فإنك توافق على استخدامنا لملفات تعريف الارتباط.',
            'en' => 'We use cookies to improve your site experience and remember your preferences. You can disable them from your browser settings. By continuing to browse, you agree to our use of cookies.',
        ];

        $row = DB::table('business_settings')->where('key', 'cookies')->first();
        if (!$row) {
            DB::table('business_settings')->insert([
                'key' => 'cookies',
                'value' => json_encode([
                    'status' => 0,
                    'text' => $defaults,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Cache::forget('cache_business_settings_table');
            return;
        }

        $decoded = json_decode($row->value, true);
        $status = $decoded['status'] ?? 0;
        $textRaw = $decoded['text'] ?? null;

        $needsUpdate = false;
        if (is_array($textRaw)) {
            foreach (['ar', 'en'] as $lang) {
                $current = trim($textRaw[$lang] ?? '');
                if ($current === '') {
                    $textRaw[$lang] = $defaults[$lang] ?? $defaults['ar'];
                    $needsUpdate = true;
                }
            }
        } else {
            $textRaw = $defaults;
            $needsUpdate = true;
        }

        if ($needsUpdate) {
            DB::table('business_settings')
                ->where('key', 'cookies')
                ->update([
                    'value' => json_encode(['status' => $status, 'text' => $textRaw]),
                    'updated_at' => now(),
                ]);
            Cache::forget('cache_business_settings_table');
        }
    }

    public function down(): void
    {
        // لا نرجع — قد يكون المستخدم عدّل القيم
    }
};
