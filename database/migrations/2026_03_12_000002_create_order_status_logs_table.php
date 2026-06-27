<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * سجل تغييرات الطلبات (Order Status Log) — حسب PLAN-SYSTEM-IMPROVEMENTS.md المرحلة 3
 * تسجيل من غيّر حالة الطلب ومتى.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->string('changed_by_type', 30)->nullable()->comment('admin, branch, system');
            $table->unsignedBigInteger('changed_by_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
    }
};
