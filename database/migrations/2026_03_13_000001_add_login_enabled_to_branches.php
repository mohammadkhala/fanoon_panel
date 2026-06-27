<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * تعطيل تسجيل دخول الفرع — النظام يعمل بلوحة المشرف فقط.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->boolean('login_enabled')->default(false)->after('status');
        });

        // تعطيل الدخول لجميع الفروع الحالية
        \Illuminate\Support\Facades\DB::table('branches')->update(['login_enabled' => false]);
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('login_enabled');
        });
    }
};
