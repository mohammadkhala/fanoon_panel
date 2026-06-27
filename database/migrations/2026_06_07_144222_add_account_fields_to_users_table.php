<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // individual | company | admin
            $table->string('account_type')->default('individual')->after('email');
            $table->string('phone')->nullable()->after('account_type');
            // company accounts: pending | approved | rejected
            $table->string('company_status')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_type', 'phone', 'company_status']);
        });
    }
};
