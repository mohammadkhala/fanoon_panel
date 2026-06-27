<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Remove recaptcha from business_settings — Recaptcha feature has been removed.
     */
    public function up(): void
    {
        DB::table('business_settings')->where('key', 'recaptcha')->delete();
    }

    public function down(): void
    {
        DB::table('business_settings')->insert([
            'key' => 'recaptcha',
            'value' => json_encode(['status' => 0, 'site_key' => '', 'secret_key' => '']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
