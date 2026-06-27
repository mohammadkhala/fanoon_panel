<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            // بيانات الـ canvas (fabric.js) لتصميم الزبون — تتيح للأدمن التعديل عليه وإضافته كقالب
            $table->longText('design_json')->nullable()->after('design_image');
            $table->unsignedSmallInteger('design_width')->nullable()->after('design_json');
            $table->unsignedSmallInteger('design_height')->nullable()->after('design_width');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn(['design_json', 'design_width', 'design_height']);
        });
    }
};
