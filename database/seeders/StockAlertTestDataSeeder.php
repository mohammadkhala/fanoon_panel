<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * تحديث بيانات مناسبة لاختبار تنبيه المخزون الأدنى:
 * - التأكد من وجود إعداد default_minimum_stock_alert = 5
 * - تحديث أول 4 منتجات ليكون مخزونها منخفضاً (لظهورها في القائمة المنسدلة)
 */
class StockAlertTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 1. إعداد حد التنبيه الافتراضي = 5
        $exists = DB::table('business_settings')->where('key', 'default_minimum_stock_alert')->exists();
        if ($exists) {
            DB::table('business_settings')->where('key', 'default_minimum_stock_alert')->update(['value' => '5', 'updated_at' => $now]);
        } else {
            DB::table('business_settings')->insert([
                'key' => 'default_minimum_stock_alert',
                'value' => '5',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. تحديث منتجات لاختبار المخزون المنخفض (أول 4 منتجات)
        $productIds = DB::table('products')->orderBy('id')->limit(4)->pluck('id');
        $stocks = [
            ['total_stock' => 2, 'minimum_stock_alert' => 5],   // تحت الحد
            ['total_stock' => 0, 'minimum_stock_alert' => 3],   // نفد
            ['total_stock' => 3, 'minimum_stock_alert' => null], // يستخدم الافتراضي 5
            ['total_stock' => 1, 'minimum_stock_alert' => 10],  // تحت حد 10
        ];

        foreach ($productIds as $i => $id) {
            $data = $stocks[$i] ?? ['total_stock' => 2, 'minimum_stock_alert' => 5];
            DB::table('products')->where('id', $id)->update([
                'total_stock' => $data['total_stock'],
                'minimum_stock_alert' => $data['minimum_stock_alert'],
                'updated_at' => $now,
            ]);
        }

        $this->command->info('تم تحديث بيانات اختبار تنبيه المخزون: إعداد افتراضي 5، و ' . count($productIds) . ' منتجات بمخزون منخفض.');
    }
}
