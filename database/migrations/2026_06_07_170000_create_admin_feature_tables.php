<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // مناطق التوصيل
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('eta')->nullable(); // مثل: 3–5 أيام
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // التقييمات
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        // الزوار والإحصائيات
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);
            $table->string('ip', 45)->nullable();
            $table->string('referer', 500)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('visited_on');
            $table->timestamps();
            $table->index('visited_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('delivery_zones');
    }
};
