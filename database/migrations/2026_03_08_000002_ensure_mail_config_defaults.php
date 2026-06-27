<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure mail_config exists in business_settings with default values.
     * Only inserts if the key does not exist.
     */
    public function up(): void
    {
        $exists = DB::table('business_settings')->where('key', 'mail_config')->exists();

        if (!$exists) {
            $defaults = json_encode([
                'status' => 0,
                'name' => 'Elite Vape',
                'host' => 'smtp.gmail.com',
                'driver' => 'smtp',
                'port' => '587',
                'username' => '',
                'email_id' => '',
                'encryption' => 'tls',
                'password' => '',
            ]);

            DB::table('business_settings')->insert([
                'key' => 'mail_config',
                'value' => $defaults,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // لا نحذف - قد يحتوي على إعدادات المستخدم
    }
};
