<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add user_type_id and requested_user_type_id to users table.
     * Required for: registration, customer management, SystemController storeData.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_type_id')) {
                $table->unsignedBigInteger('user_type_id')->nullable()->after('login_medium');
            }
            if (!Schema::hasColumn('users', 'requested_user_type_id')) {
                $table->unsignedBigInteger('requested_user_type_id')->nullable()->after('user_type_id');
            }
        });
        // No foreign keys — avoids errno 121 on shared hosting; columns suffice for registration.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = array_filter(['user_type_id', 'requested_user_type_id'], fn ($c) => Schema::hasColumn('users', $c));
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
