<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Add visible_to_user_types JSON column to products.
     *
     * NULL  → visible to everyone (all user types + guests)
     * []    → hidden from everyone
     * [1,3] → visible only to user types with those IDs
     *
     * A special sentinel value of 0 in the array means "guests only".
     * Use NULL as the default so existing products stay public.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('visible_to_user_types')->nullable()->after('status')
                  ->comment('NULL = all; [] = none; [id,...] = specific user_type IDs; include 0 for guests');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('visible_to_user_types');
        });
    }
};
