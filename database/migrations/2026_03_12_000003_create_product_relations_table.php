<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * منتجات ذات صلة (Related Products) — حسب PLAN-SYSTEM-IMPROVEMENTS.md القسم 4.2
 * ربط المنتجات ببعضها لاقتراح منتجات ذات صلة تلقائياً.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('related_product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'related_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_relations');
    }
};
