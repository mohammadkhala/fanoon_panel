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
        Schema::table('designs', function (Blueprint $table) {
            // fabric_json is no longer required — designs are now uploaded files
            $table->json('fabric_json')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->json('fabric_json')->nullable(false)->change();
        });
    }
};
