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
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('order_status');
            $table->index('branch_id');
            $table->index('created_at');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['order_status']);
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['created_at']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });
    }
};
