<?php

namespace Database\Seeders;

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;

class FlashSaleSeeder extends Seeder
{
    public function run(): void
    {
        $flashSales = [
            [
                'title' => 'عرض نهاية الأسبوع',
                'start_date' => now()->startOfWeek()->format('Y-m-d H:i:s'),
                'end_date' => now()->endOfWeek()->format('Y-m-d H:i:s'),
                'status' => 1,
            ],
            [
                'title' => 'عروض السجائر الإلكترونية',
                'start_date' => now()->format('Y-m-d 00:00:00'),
                'end_date' => now()->addWeeks(2)->format('Y-m-d 23:59:59'),
                'status' => 1,
            ],
            [
                'title' => 'خصومات السوائل',
                'start_date' => now()->addDays(3)->format('Y-m-d 00:00:00'),
                'end_date' => now()->addWeeks(3)->format('Y-m-d 23:59:59'),
                'status' => 0,
            ],
            [
                'title' => 'عرض الصيف الكبير',
                'start_date' => now()->subDays(5)->format('Y-m-d 00:00:00'),
                'end_date' => now()->addMonth()->format('Y-m-d 23:59:59'),
                'status' => 1,
            ],
            [
                'title' => 'عروض الإكسسوارات',
                'start_date' => now()->addWeek()->format('Y-m-d 00:00:00'),
                'end_date' => now()->addWeeks(4)->format('Y-m-d 23:59:59'),
                'status' => 0,
            ],
        ];

        $productIds = Product::pluck('id')->toArray();

        foreach ($flashSales as $data) {
            $flashSale = FlashSale::firstOrCreate(
                ['title' => $data['title']],
                array_merge($data, ['image' => 'def.png'])
            );

            if ($flashSale->wasRecentlyCreated && !empty($productIds)) {
                $count = min(3, count($productIds));
                $ids = $productIds;
                shuffle($ids);
                $selectedIds = array_slice($ids, 0, $count);
                foreach ($selectedIds as $productId) {
                    FlashSaleProduct::firstOrCreate(
                        [
                            'flash_sale_id' => $flashSale->id,
                            'product_id' => $productId,
                        ]
                    );
                }
            }
        }

        $this->command->info('تم إضافة ' . count($flashSales) . ' عروض سعر إلى قاعدة البيانات.');
    }
}
