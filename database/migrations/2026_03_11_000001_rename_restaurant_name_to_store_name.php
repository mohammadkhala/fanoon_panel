<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * توحيد المصطلحات: restaurant_name → store_name (النظام تجارة إلكترونية وليس مطاعم)
     */
    public function up(): void
    {
        DB::table('business_settings')
            ->where('key', 'restaurant_name')
            ->update(['key' => 'store_name']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('business_settings')
            ->where('key', 'store_name')
            ->update(['key' => 'restaurant_name']);
    }
};
