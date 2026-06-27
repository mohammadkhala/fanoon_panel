<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add loyalty_and_coupon_together setting.
     * 1 = allow both in same order (default), 0 = one only.
     */
    public function up(): void
    {
        $exists = DB::table('business_settings')->where('key', 'loyalty_and_coupon_together')->exists();
        if (!$exists) {
            DB::table('business_settings')->insert([
                'key' => 'loyalty_and_coupon_together',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('business_settings')->where('key', 'loyalty_and_coupon_together')->delete();
    }
};
