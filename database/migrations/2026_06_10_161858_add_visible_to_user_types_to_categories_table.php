<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add visible_to_user_types JSON column to categories.
     *
     * NULL  → visible to everyone (all user types + guests)
     * []    → hidden from everyone
     * [1,3] → visible only to user types with those IDs
     *
     * A special sentinel value of [0] means "guests only".
     * Use NULL as the default so existing categories stay public.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->json('visible_to_user_types')->nullable()->after('is_featured')
                  ->comment('NULL = all; [] = none; [id,...] = specific user_type IDs; include 0 for guests');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('visible_to_user_types');
        });
    }
};
