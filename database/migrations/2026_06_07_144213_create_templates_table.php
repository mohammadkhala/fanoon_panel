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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->nullable();        // sports | corporate | academic | occasion ...
            $table->string('preview_image')->nullable();   // path to product photo
            $table->json('fabric_json')->nullable();        // base editor canvas
            $table->decimal('retail_price', 8, 2)->default(0);
            $table->decimal('wholesale_price', 8, 2)->default(0);
            $table->string('badge')->nullable();            // "الأكثر طلباً" | "جديد"
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
