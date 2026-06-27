<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete()->after('product_template_id');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete()->after('product_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
    }
};
