<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_zone_id')->nullable()->after('user_id')
                ->constrained('delivery_zones')->nullOnDelete();
            $table->decimal('delivery_fee', 8, 2)->default(0)->after('delivery_zone_id');
            $table->string('contact_email')->nullable()->after('contact_phone');
            $table->string('shipping_city', 100)->nullable()->after('contact_email');
            $table->string('shipping_address', 255)->nullable()->after('shipping_city');
            $table->string('shipping_neighborhood', 100)->nullable()->after('shipping_address');
            $table->string('shipping_building', 100)->nullable()->after('shipping_neighborhood');
            $table->string('payment_method')->default('cod')->after('shipping_building');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_zone_id']);
            $table->dropColumn([
                'delivery_zone_id',
                'delivery_fee',
                'contact_email',
                'shipping_city',
                'shipping_address',
                'shipping_neighborhood',
                'shipping_building',
                'payment_method',
            ]);
        });
    }
};
