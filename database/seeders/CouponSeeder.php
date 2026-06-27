<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'title' => 'خصم ترحيبي 10%',
                'code' => 'WELCOME10',
                'limit' => 100,
                'coupon_type' => 'default',
                'start_date' => now()->format('Y-m-d'),
                'expire_date' => now()->addMonths(3)->format('Y-m-d'),
                'min_purchase' => 50,
                'max_discount' => 30,
                'discount' => 10,
                'discount_type' => 'percent',
                'status' => 1,
            ],
            [
                'title' => 'خصم أول طلب 20%',
                'code' => 'FIRST20',
                'limit' => 1,
                'coupon_type' => 'first_order',
                'start_date' => now()->format('Y-m-d'),
                'expire_date' => now()->addMonths(6)->format('Y-m-d'),
                'min_purchase' => 100,
                'max_discount' => 0,
                'discount' => 20,
                'discount_type' => 'percent',
                'status' => 1,
            ],
            [
                'title' => 'خصم 25 شيكل',
                'code' => 'SAVE25',
                'limit' => 50,
                'coupon_type' => 'default',
                'start_date' => now()->format('Y-m-d'),
                'expire_date' => now()->addMonth()->format('Y-m-d'),
                'min_purchase' => 150,
                'max_discount' => 0,
                'discount' => 25,
                'discount_type' => 'amount',
                'status' => 1,
            ],
            [
                'title' => 'عرض الصيف 15%',
                'code' => 'SUMMER15',
                'limit' => 200,
                'coupon_type' => 'default',
                'start_date' => now()->format('Y-m-d'),
                'expire_date' => now()->addMonths(2)->format('Y-m-d'),
                'min_purchase' => 75,
                'max_discount' => 50,
                'discount' => 15,
                'discount_type' => 'percent',
                'status' => 1,
            ],
            [
                'title' => 'خصم فيب 30%',
                'code' => 'VAPE30',
                'limit' => 80,
                'coupon_type' => 'default',
                'start_date' => now()->format('Y-m-d'),
                'expire_date' => now()->addWeeks(4)->format('Y-m-d'),
                'min_purchase' => 120,
                'max_discount' => 60,
                'discount' => 30,
                'discount_type' => 'percent',
                'status' => 1,
            ],
        ];

        foreach ($coupons as $data) {
            Coupon::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }

        $this->command->info('تم إضافة ' . count($coupons) . ' كوبونات إلى قاعدة البيانات.');
    }
}
