<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * مثال هرمية التصنيفات: أب → ابن → ابن ابن
 * للتحقق من عرض البيانات في الصفحات.
 *
 * التشغيل: php artisan db:seed --class=CategoryHierarchyExampleSeeder
 */
class CategoryHierarchyExampleSeeder extends Seeder
{
    public function run(): void
    {
        // أب (جذر)
        $cat1 = Category::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'سجائر إلكترونية',
                'parent_id' => 0,
                'position' => 0,
                'status' => 1,
                'image' => 'def.png',
                'banner_image' => null,
                'is_featured' => 1,
            ]
        );

        // ابن
        $cat2 = Category::firstOrCreate(
            ['id' => 2],
            [
                'name' => 'سوائل نيكوتين',
                'parent_id' => 1,
                'position' => 1,
                'status' => 1,
                'image' => 'def.png',
                'banner_image' => null,
                'is_featured' => 0,
            ]
        );

        // ابن ابن (تحت سوائل نيكوتين)
        $cat4 = Category::firstOrCreate(
            ['name' => 'سوائل نيكوتين ملح', 'parent_id' => 2],
            [
                'parent_id' => 2,
                'position' => 1,
                'status' => 1,
                'image' => 'def.png',
                'banner_image' => null,
                'is_featured' => 0,
            ]
        );

        // منتج في ابن ابن — يظهر عند تصفح أب أو ابن أو ابن ابن
        $categoryIds4 = json_encode([
            ['id' => (string) $cat1->id, 'position' => 1],
            ['id' => (string) $cat2->id, 'position' => 2],
            ['id' => (string) $cat4->id, 'position' => 3],
        ]);

        Product::firstOrCreate(
            ['name' => 'سائل ملح نيكوتين 20mg'],
            [
                'description' => '<p>منتج في تصنيف فرعي فرعي (ابن ابن) — للتجربة</p>',
                'image' => json_encode(['def.png']),
                'price' => 55,
                'variations' => json_encode([['type' => 'default', 'price' => 55, 'stock' => 15]]),
                'tax' => 0,
                'status' => 1,
                'attributes' => json_encode([]),
                'category_ids' => $categoryIds4,
                'choice_options' => json_encode([]),
                'discount' => 0,
                'discount_type' => 'percent',
                'tax_type' => 'percent',
                'unit' => 'pc',
                'total_stock' => 15,
                'min_order_qty' => 1,
                'minimum_stock_alert' => 5,
            ]
        );

        $this->command->info('تم إضافة مثال الهرمية: أب (سجائر إلكترونية) → ابن (سوائل نيكوتين) → ابن ابن (سوائل نيكوتين ملح) + منتج.');
    }
}
