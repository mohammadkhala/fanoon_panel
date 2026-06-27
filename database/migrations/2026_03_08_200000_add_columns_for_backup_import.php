<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('title', 100)->nullable();
                $table->string('image', 100)->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('banner_type', 255)->default('primary');
                $table->string('placement', 64)->nullable();
                $table->index('placement');
            });
        }
        if (!Schema::hasColumn('categories', 'slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('slug', 191)->nullable()->after('name');
                $table->unsignedTinyInteger('level')->default(1)->after('parent_id');
            });
        }
        if (!Schema::hasColumn('products', 'product_brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('product_brand_id')->nullable()->after('id');
                $table->unsignedInteger('view_count')->default(0)->after('minimum_stock_alert');
                $table->unsignedInteger('sales_count')->default(0)->after('view_count');
            });
        }
        if (!Schema::hasColumn('cities', 'area_id')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->unsignedBigInteger('area_id')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
        if (Schema::hasColumn('categories', 'slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn(['slug', 'level']);
            });
        }
        if (Schema::hasColumn('products', 'product_brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['product_brand_id', 'view_count', 'sales_count']);
            });
        }
        if (Schema::hasColumn('cities', 'area_id')) {
            Schema::table('cities', function (Blueprint $table) {
                $table->dropColumn('area_id');
            });
        }
    }
};
