<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'minimum_stock_alert')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('minimum_stock_alert')->nullable()->after('min_order_qty');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'minimum_stock_alert')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('minimum_stock_alert');
            });
        }
    }
};
