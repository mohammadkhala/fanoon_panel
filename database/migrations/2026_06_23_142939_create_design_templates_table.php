<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('design_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('category', 100)->nullable();
            $table->longText('canvas_json');
            $table->string('thumbnail')->nullable();
            $table->unsignedSmallInteger('canvas_width')->default(800);
            $table->unsignedSmallInteger('canvas_height')->default(800);
            $table->boolean('status')->default(1);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('design_templates');
    }
};
