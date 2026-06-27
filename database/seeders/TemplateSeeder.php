<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'درع التميّز الكلاسيكي',
                'category' => 'corporate',
                'description' => 'تصميم أنيق للمناسبات الرسمية وحفلات التكريم',
                'preview_image' => 'templates/t0.png',
                'retail_price' => 85, 'wholesale_price' => 55,
                'badge' => 'الأكثر طلباً', 'rating' => 5.0, 'reviews_count' => 128,
            ],
            [
                'name' => 'درع البطولة الذهبي',
                'category' => 'sports',
                'description' => 'مثالي للبطولات الرياضية والإنجازات الاستثنائية',
                'preview_image' => 'templates/t1.png',
                'retail_price' => 95, 'wholesale_price' => 62,
                'badge' => null, 'rating' => 5.0, 'reviews_count' => 96,
            ],
            [
                'name' => 'درع الشكر والتقدير',
                'category' => 'occasion',
                'description' => 'هدية راقية للتعبير عن الامتنان الصادق',
                'preview_image' => 'templates/t2.png',
                'retail_price' => 75, 'wholesale_price' => 48,
                'badge' => null, 'rating' => 4.5, 'reviews_count' => 74,
            ],
            [
                'name' => 'درع التخرّج',
                'category' => 'academic',
                'description' => 'لتكريم الطلاب المتفوقين وحفلات التخرّج',
                'preview_image' => 'templates/t3.png',
                'retail_price' => 80, 'wholesale_price' => 52,
                'badge' => 'جديد', 'rating' => 5.0, 'reviews_count' => 41,
            ],
            [
                'name' => 'درع الميدالية الذهبية',
                'category' => 'sports',
                'description' => 'للفائزين والمراكز الأولى في المسابقات',
                'preview_image' => 'templates/t1.png',
                'retail_price' => 90, 'wholesale_price' => 58,
                'badge' => null, 'rating' => 5.0, 'reviews_count' => 63,
            ],
            [
                'name' => 'درع المناسبات',
                'category' => 'occasion',
                'description' => 'للأعراس والمناسبات الدينية والاجتماعية',
                'preview_image' => 'templates/t2.png',
                'retail_price' => 70, 'wholesale_price' => 45,
                'badge' => null, 'rating' => 4.5, 'reviews_count' => 38,
            ],
        ];

        foreach ($templates as $i => $t) {
            Template::updateOrCreate(
                ['slug' => Str::slug($t['name']) . '-' . ($i + 1)],
                array_merge($t, [
                    'sort_order' => $i,
                    'is_active' => true,
                    'fabric_json' => null,
                ])
            );
        }
    }
}
