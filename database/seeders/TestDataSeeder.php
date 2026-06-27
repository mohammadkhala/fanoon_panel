<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Category;
use App\Models\City;
use App\Models\LoyaltyPoint;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderShipment;
use App\Models\Product;
use App\Models\ShippingCompany;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * بيانات اختبار للتأكد من عمل التطبيق — تصنيفات، منتجات، عملاء، طلبات، شركات شحن، مناطق، نقاط ولاء.
 */
class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();
        if (!$branch) {
            $this->command->warn('لا يوجد فرع — نفّذ migrations و AdminTableSeeder أولاً.');
            return;
        }

        // 1. تصنيفات
        $cat1 = Category::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'سجائر إلكترونية',
                'parent_id' => 0,
                'position' => 0,
                'status' => 1,
                'image' => 'def.png',
                'banner_image' => 'def.png',
                'is_featured' => 1,
            ]
        );
        $cat2 = Category::firstOrCreate(
            ['id' => 2],
            [
                'name' => 'سوائل نيكوتين',
                'parent_id' => 1,
                'position' => 1,
                'status' => 1,
                'image' => 'def.png',
                'banner_image' => 'def.png',
                'is_featured' => 0,
            ]
        );
        $cat3 = Category::firstOrCreate(
            ['id' => 3],
            [
                'name' => 'إكسسوارات',
                'parent_id' => 0,
                'position' => 0,
                'status' => 1,
                'image' => 'def.png',
                'banner_image' => 'def.png',
                'is_featured' => 1,
            ]
        );

        // 2. منتجات
        $categoryIds = json_encode([['id' => (string) $cat1->id, 'position' => 1], ['id' => (string) $cat2->id, 'position' => 2]]);
        $categoryIds3 = json_encode([['id' => (string) $cat3->id, 'position' => 1]]);
        $variations = json_encode([['type' => 'default', 'price' => 50, 'stock' => 100]]);
        $image = json_encode(['def.png']);
        $attrs = json_encode([]);
        $choiceOpts = json_encode([]);

        $products = [
            ['name' => 'Vape Pod 2000', 'price' => 89.99, 'stock' => 25, 'min_alert' => 5, 'category_ids' => $categoryIds],
            ['name' => 'سائل نيكوتين 30ml', 'price' => 45, 'stock' => 3, 'min_alert' => 10, 'category_ids' => $categoryIds],
            ['name' => 'سائل نيكوتين 60ml', 'price' => 75, 'stock' => 50, 'min_alert' => 5, 'category_ids' => $categoryIds],
            ['name' => 'شاحن USB للفيب', 'price' => 15, 'stock' => 2, 'min_alert' => 5, 'category_ids' => $categoryIds3],
            ['name' => 'فم بديل Pod', 'price' => 12, 'stock' => 40, 'min_alert' => 10, 'category_ids' => $categoryIds3],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['name' => $p['name']],
                [
                    'description' => '<p>منتج اختبار - ' . $p['name'] . '</p>',
                    'image' => $image,
                    'price' => $p['price'],
                    'variations' => $variations,
                    'tax' => 0,
                    'status' => 1,
                    'attributes' => $attrs,
                    'category_ids' => $p['category_ids'],
                    'choice_options' => $choiceOpts,
                    'discount' => 0,
                    'discount_type' => 'percent',
                    'tax_type' => 'percent',
                    'unit' => 'pc',
                    'total_stock' => $p['stock'],
                    'min_order_qty' => 1,
                    'minimum_stock_alert' => $p['min_alert'],
                ]
            );
        }

        // 3. عملاء (تجنب id=0 وهو Walk-In)
        $users = [];
        foreach (['أحمد محمد', 'سارة علي', 'خالد حسن'] as $i => $name) {
            $parts = explode(' ', $name);
            $email = 'customer' . ($i + 1) . '@test.com';
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'f_name' => $parts[0],
                    'l_name' => $parts[1] ?? '',
                    'phone' => '0599' . (100000 + $i),
                    'password' => bcrypt('12345678'),
                    'is_phone_verified' => 0,
                    'login_medium' => 'general',
                ]
            );
            $users[] = $user;
        }

        // 4. طلبات مع تفاصيل
        $productIds = Product::pluck('id')->toArray();
        $statuses = ['pending', 'confirmed', 'processing', 'out_for_delivery', 'delivered', 'returned', 'failed'];
        $orderCount = Order::where('order_type', '!=', 'pos')->count();

        for ($i = 1; $i <= 12; $i++) {
            $user = $users[array_rand($users)];
            $status = $statuses[array_rand($statuses)];
            $amount = rand(50, 300) + (rand(0, 99) / 100);
            $txnRef = 'TST-' . time() . '-' . $i;
            $order = Order::firstOrCreate(
                ['transaction_reference' => $txnRef],
                [
                    'user_id' => $user->id,
                    'order_amount' => $amount,
                    'order_status' => $status,
                    'is_guest' => 0,
                    'coupon_discount_amount' => 0,
                    'payment_status' => in_array($status, ['delivered', 'returned']) ? 'paid' : 'unpaid',
                    'total_tax_amount' => 0,
                    'payment_method' => 'cash_on_delivery',
                    'checked' => 0,
                    'delivery_charge' => 10,
                    'order_type' => $i % 4 === 0 ? 'self_pickup' : 'delivery',
                    'branch_id' => $branch->id,
                    'extra_discount' => 0,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]
            );

            if ($order->wasRecentlyCreated && $order->details()->count() === 0) {
                $pid = $productIds[array_rand($productIds)];
                $product = Product::find($pid);
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $pid,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                    'discount_on_product' => 0,
                    'tax_amount' => 0,
                    'variant' => '',
                    'unit' => 'pc',
                    'is_stock_decreased' => 1,
                ]);
            }
        }

        // 5. شركات شحن
        $shippingCompanies = ['Aramex', 'DHL', 'البريد السريع'];
        foreach ($shippingCompanies as $idx => $name) {
            ShippingCompany::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'is_active' => true,
                    'sort_order' => $idx + 1,
                ]
            );
        }

        // 6. مناطق ومدن
        $area = Area::firstOrCreate(
            ['branch_id' => $branch->id, 'name_en' => 'الخليل'],
            ['name_ar' => 'الخليل', 'delivery_charge' => 15, 'sort_order' => 0]
        );
        City::firstOrCreate(
            ['area_id' => $area->id, 'name' => 'الخليل'],
            ['name_ar' => 'الخليل', 'sort_order' => 0]
        );
        City::firstOrCreate(
            ['area_id' => $area->id, 'name' => 'بيت لحم'],
            ['name_ar' => 'بيت لحم', 'sort_order' => 1]
        );

        // 7. نقاط ولاء لعميل واحد
        $userWithLoyalty = $users[0];
        LoyaltyPoint::updateOrCreate(
            ['user_id' => $userWithLoyalty->id],
            [
                'points' => 150,
                'level' => 'bronze',
                'total_spent' => 450,
            ]
        );

        // 8. شحنة نموذجية لطلب مُسلّم
        $deliveredOrder = Order::where('order_type', '!=', 'pos')
            ->where('order_status', 'delivered')
            ->first();
        if ($deliveredOrder) {
            $sc = ShippingCompany::first();
            if ($sc && $deliveredOrder->orderShipments()->count() === 0) {
                OrderShipment::firstOrCreate(
                    [
                        'order_id' => $deliveredOrder->id,
                        'shipping_company_id' => $sc->id,
                    ],
                    [
                        'tracking_number' => 'TRK-' . $deliveredOrder->id . '-001',
                        'status' => 'delivered',
                        'shipped_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('تم إضافة بيانات الاختبار: تصنيفات، منتجات، عملاء، طلبات، شركات شحن، مناطق، نقاط ولاء.');
    }
}
