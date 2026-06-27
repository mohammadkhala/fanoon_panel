<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Attribute;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyPointLog;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderShipment;
use App\Models\Product;
use App\Models\Review;
use App\Models\ShippingCompany;
use App\Models\SocialMedia;
use App\Models\User;
use App\Models\UserType;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * بيانات تجريبية كاملة لجميع جداول قاعدة البيانات.
 * للتشغيل: php artisan db:seed --class=FullTestDataSeeder
 *
 * يتطلب: migrations + BaitPaitSeeder (فرع ومشرف)
 */
class FullTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first();
        if (!$branch) {
            $this->command->error('لا يوجد فرع. نفّذ: php artisan migrate:fresh --seed');
            return;
        }

        $this->seedUserTypes();
        $this->seedAttributes();
        $this->seedCategories();
        $this->seedProducts($branch);
        $this->seedAreasAndCities($branch);
        $this->seedDeliveryCharges($branch);
        $this->seedUsers();
        $this->seedCustomerAddresses();
        $this->seedCoupons();
        $this->seedFlashSales();
        $this->seedOrders($branch);
        $this->seedShippingAndShipments();
        $this->seedProductUserTypePrices();
        $this->seedLoyalty();
        $this->seedLoyaltyEnabled();
        $this->seedConversationsAndMessages();
        $this->seedReviews();
        $this->seedWishlists();
        $this->seedContactUs();
        $this->seedSocialMedias();
        $this->seedNotifications();
        $this->seedBanners();
        $this->seedTags();
        $this->seedOrderStatusLogs();

        $this->command->info('تم إضافة بيانات تجريبية كاملة لجميع الجداول.');
    }

    private function seedUserTypes(): void
    {
        if (DB::table('user_types')->count() > 0) {
            return;
        }
        $now = now();
        DB::table('user_types')->insert([
            ['name' => 'عميل عادي', 'is_default' => true, 'position' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'عميل مميز', 'is_default' => false, 'position' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'تاجر جملة', 'is_default' => false, 'position' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedAttributes(): void
    {
        if (DB::table('attributes')->count() > 0) {
            return;
        }
        $attrs = ['النكهة', 'القوة', 'الحجم', 'اللون', 'النوع'];
        foreach ($attrs as $name) {
            Attribute::firstOrCreate(['name' => $name]);
        }
    }

    private function seedCategories(): void
    {
        if (DB::table('categories')->count() > 0) {
            return;
        }
        $now = now();
        $cats = [
            ['name' => 'سجائر إلكترونية', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'is_featured' => 1],
            ['name' => 'سوائل نيكوتين', 'parent_id' => 0, 'position' => 1, 'status' => 1, 'is_featured' => 1],
            ['name' => 'إكسسوارات', 'parent_id' => 0, 'position' => 2, 'status' => 1, 'is_featured' => 0],
            ['name' => 'بود وأنظمة مغلقة', 'parent_id' => 0, 'position' => 3, 'status' => 1, 'is_featured' => 1],
            ['name' => 'شواحن وبطاريات', 'parent_id' => 0, 'position' => 4, 'status' => 1, 'is_featured' => 0],
        ];
        foreach ($cats as $c) {
            Category::create(array_merge($c, [
                'image' => 'def.png',
                'banner_image' => 'def.png',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    private function seedProducts(Branch $branch): void
    {
        if (DB::table('products')->count() > 5) {
            return;
        }
        $categories = Category::all();
        $catIds1 = json_encode([['id' => (string) $categories[0]->id, 'position' => 1]]);
        $catIds2 = json_encode([['id' => (string) $categories[1]->id, 'position' => 1]]);
        $catIds3 = json_encode([['id' => (string) $categories[2]->id, 'position' => 1]]);
        $catIds4 = json_encode([['id' => (string) $categories[3]->id, 'position' => 1]]);
        $catIds12 = json_encode([
            ['id' => (string) $categories[0]->id, 'position' => 1],
            ['id' => (string) $categories[1]->id, 'position' => 2],
        ]);
        $variations = json_encode([['type' => 'default', 'price' => 0, 'stock' => 100]]);
        $image = json_encode(['def.png']);
        $attrs = json_encode([]);
        $choiceOpts = json_encode([]);

        $products = [
            ['name' => 'Vape Pod 2000', 'price' => 89.99, 'stock' => 25, 'discount' => 10, 'category_ids' => $catIds1],
            ['name' => 'سائل نيكوتين 30ml - منعش', 'price' => 45, 'stock' => 50, 'discount' => 0, 'category_ids' => $catIds2],
            ['name' => 'سائل نيكوتين 60ml', 'price' => 75, 'stock' => 40, 'discount' => 15, 'category_ids' => $catIds2],
            ['name' => 'شاحن USB للفيب', 'price' => 15, 'stock' => 60, 'discount' => 0, 'category_ids' => $catIds3],
            ['name' => 'فم بديل Pod', 'price' => 12, 'stock' => 80, 'discount' => 5, 'category_ids' => $catIds3],
            ['name' => 'Vaporesso XROS 3', 'price' => 120, 'stock' => 15, 'discount' => 20, 'category_ids' => $catIds4],
            ['name' => 'ELFBAR 600', 'price' => 35, 'stock' => 100, 'discount' => 0, 'category_ids' => $catIds4],
            ['name' => 'بطارية 18650', 'price' => 22, 'stock' => 45, 'discount' => 0, 'category_ids' => $catIds3],
            ['name' => 'سائل سالت نيك 20mg', 'price' => 55, 'stock' => 30, 'discount' => 10, 'category_ids' => $catIds2],
            ['name' => 'جهاز فيب متقدم', 'price' => 199, 'stock' => 8, 'discount' => 25, 'category_ids' => $catIds12],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['name' => $p['name']],
                [
                    'description' => '<p>منتج تجريبي - ' . $p['name'] . '</p><p>جودة عالية ومناسب للاستخدام اليومي.</p>',
                    'image' => $image,
                    'price' => $p['price'],
                    'variations' => $variations,
                    'tax' => 0,
                    'status' => 1,
                    'attributes' => $attrs,
                    'category_ids' => $p['category_ids'],
                    'choice_options' => $choiceOpts,
                    'discount' => $p['discount'],
                    'discount_type' => 'percent',
                    'tax_type' => 'percent',
                    'unit' => 'pc',
                    'total_stock' => $p['stock'],
                    'min_order_qty' => 1,
                    'minimum_stock_alert' => 5,
                ]
            );
        }
    }

    private function seedAreasAndCities(Branch $branch): void
    {
        if (Area::where('branch_id', $branch->id)->count() > 0) {
            return;
        }
        $areas = [
            ['name_en' => 'Hebron', 'name_ar' => 'الخليل', 'delivery_charge' => 15],
            ['name_en' => 'Bethlehem', 'name_ar' => 'بيت لحم', 'delivery_charge' => 12],
            ['name_en' => 'Ramallah', 'name_ar' => 'رام الله', 'delivery_charge' => 20],
            ['name_en' => 'Nablus', 'name_ar' => 'نابلس', 'delivery_charge' => 18],
            ['name_en' => 'Jerusalem', 'name_ar' => 'القدس', 'delivery_charge' => 25],
        ];
        foreach ($areas as $i => $a) {
            $area = Area::create([
                'branch_id' => $branch->id,
                'name_en' => $a['name_en'],
                'name_ar' => $a['name_ar'],
                'delivery_charge' => $a['delivery_charge'],
                'sort_order' => $i,
            ]);
            if (Schema::hasColumn('cities', 'area_id')) {
                $cityData = [
                    'area_id' => $area->id,
                    'name' => $a['name_en'],
                    'name_ar' => $a['name_ar'],
                    'sort_order' => $i * 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('cities', 'names')) {
                    $cityData['names'] = json_encode(['ar' => $a['name_ar'], 'en' => $a['name_en']]);
                }
                DB::table('cities')->insertOrIgnore($cityData);
            }
        }
        if (DB::table('cities')->count() === 0) {
            $cityNames = ['الخليل', 'بيت لحم', 'رام الله', 'نابلس', 'القدس', 'جنين', 'طولكرم'];
            foreach ($cityNames as $i => $name) {
                DB::table('cities')->insertOrIgnore([
                    'name' => $name,
                    'sort_order' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedDeliveryCharges(Branch $branch): void
    {
        if (DB::table('delivery_charge_setups')->where('branch_id', $branch->id)->exists()) {
            return;
        }
        $now = now();
        DB::table('delivery_charge_setups')->insert([
            'branch_id' => $branch->id,
            'delivery_charge_type' => 'area',
            'delivery_charge_per_kilometer' => 2,
            'minimum_delivery_charge' => 10,
            'minimum_distance_for_free_delivery' => 0,
            'fixed_delivery_charge' => 15,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $areas = Area::where('branch_id', $branch->id)->get();
        foreach ($areas as $a) {
            DB::table('delivery_charge_by_areas')->insert([
                'branch_id' => $branch->id,
                'area_name' => $a->name_en,
                'delivery_charge' => $a->delivery_charge,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedUsers(): void
    {
        if (User::where('email', 'like', 'customer%@test.com')->count() >= 5) {
            return;
        }
        $customers = [
            ['f_name' => 'أحمد', 'l_name' => 'محمد', 'email' => 'customer1@test.com', 'phone' => '0599123456'],
            ['f_name' => 'سارة', 'l_name' => 'علي', 'email' => 'customer2@test.com', 'phone' => '0599234567'],
            ['f_name' => 'خالد', 'l_name' => 'حسن', 'email' => 'customer3@test.com', 'phone' => '0599345678'],
            ['f_name' => 'فاطمة', 'l_name' => 'أحمد', 'email' => 'customer4@test.com', 'phone' => '0599456789'],
            ['f_name' => 'محمد', 'l_name' => 'يوسف', 'email' => 'customer5@test.com', 'phone' => '0599567890'],
        ];
        foreach ($customers as $c) {
            User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'f_name' => $c['f_name'],
                    'l_name' => $c['l_name'],
                    'phone' => $c['phone'],
                    'password' => bcrypt('12345678'),
                    'is_phone_verified' => 0,
                    'login_medium' => 'general',
                ]
            );
        }
    }

    private function seedCustomerAddresses(): void
    {
        $users = User::where('email', 'like', 'customer%@test.com')->get();
        $areas = Area::all();
        if ($users->isEmpty() || $areas->isEmpty()) {
            return;
        }
        if (CustomerAddress::count() >= 5) {
            return;
        }
        $addressTypes = ['home', 'work', 'other'];
        foreach ($users->take(3) as $user) {
            $area = $areas->random();
            CustomerAddress::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'address_type' => $addressTypes[array_rand($addressTypes)],
                    'contact_person_number' => $user->phone ?? '0599000000',
                ],
                [
                    'address' => 'شارع الرئيسي، مبنى ' . rand(1, 50),
                    'contact_person_name' => $user->f_name . ' ' . $user->l_name,
                    'city' => $area->name_ar ?? $area->name_en,
                    'area_id' => $area->id,
                    'floor' => (string) rand(1, 5),
                    'house' => (string) rand(1, 100),
                    'road' => 'شارع ' . rand(1, 20),
                    'is_guest' => false,
                ]
            );
        }
    }

    private function seedCoupons(): void
    {
        if (DB::table('coupons')->count() >= 5) {
            return;
        }
        $coupons = [
            ['title' => 'خصم ترحيبي 10%', 'code' => 'WELCOME10', 'discount' => 10, 'discount_type' => 'percent', 'min_purchase' => 50, 'max_discount' => 30],
            ['title' => 'خصم 25 شيكل', 'code' => 'SAVE25', 'discount' => 25, 'discount_type' => 'amount', 'min_purchase' => 150, 'max_discount' => 0],
            ['title' => 'عرض الصيف 15%', 'code' => 'SUMMER15', 'discount' => 15, 'discount_type' => 'percent', 'min_purchase' => 75, 'max_discount' => 50],
            ['title' => 'خصم فيب 30%', 'code' => 'VAPE30', 'discount' => 30, 'discount_type' => 'percent', 'min_purchase' => 120, 'max_discount' => 60],
        ];
        $now = now();
        foreach ($coupons as $c) {
            Coupon::firstOrCreate(
                ['code' => $c['code']],
                array_merge($c, [
                    'limit' => 100,
                    'coupon_type' => 'default',
                    'start_date' => now()->format('Y-m-d'),
                    'expire_date' => now()->addMonths(3)->format('Y-m-d'),
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }

    private function seedFlashSales(): void
    {
        if (FlashSale::count() >= 2) {
            return;
        }
        $sales = [
            ['title' => 'عرض نهاية الأسبوع', 'status' => 1, 'start' => now()->startOfWeek(), 'end' => now()->endOfWeek()],
            ['title' => 'عروض السجائر الإلكترونية', 'status' => 1, 'start' => now(), 'end' => now()->addWeeks(2)],
        ];
        $productIds = Product::pluck('id')->toArray();
        foreach ($sales as $s) {
            $fs = FlashSale::firstOrCreate(
                ['title' => $s['title']],
                [
                    'status' => $s['status'],
                    'start_date' => $s['start'],
                    'end_date' => $s['end'],
                    'image' => 'def.png',
                ]
            );
            if ($fs->wasRecentlyCreated && !empty($productIds)) {
                $selected = array_slice(array_merge([], $productIds), 0, min(4, count($productIds)));
                foreach ($selected as $pid) {
                    FlashSaleProduct::firstOrCreate([
                        'flash_sale_id' => $fs->id,
                        'product_id' => $pid,
                    ]);
                }
            }
        }
    }

    private function seedOrders(Branch $branch): void
    {
        $users = User::where('email', 'like', 'customer%@test.com')->get();
        $addresses = CustomerAddress::all();
        $products = Product::all();
        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }
        $existingCount = Order::where('branch_id', $branch->id)->count();
        if ($existingCount >= 20) {
            return;
        }
        $statuses = ['pending', 'confirmed', 'processing', 'out_for_delivery', 'delivered', 'returned', 'failed', 'canceled'];
        for ($i = 1; $i <= 15; $i++) {
            $user = $users->random();
            $addr = $addresses->isEmpty() ? null : $addresses->random();
            $status = $statuses[array_rand($statuses)];
            $amount = rand(50, 350) + (rand(0, 99) / 100);
            $txnRef = 'TST-' . time() . '-' . $i . '-' . Str::random(6);
            $order = Order::firstOrCreate(
                ['transaction_reference' => $txnRef],
                [
                    'user_id' => $user->id,
                    'is_guest' => 0,
                    'order_amount' => $amount,
                    'coupon_discount_amount' => rand(0, 1) ? rand(5, 20) : 0,
                    'payment_status' => in_array($status, ['delivered', 'returned']) ? 'paid' : 'unpaid',
                    'order_status' => $status,
                    'total_tax_amount' => 0,
                    'payment_method' => 'cash_on_delivery',
                    'transaction_reference' => $txnRef,
                    'delivery_address_id' => $addr?->id,
                    'delivery_charge' => 15,
                    'order_type' => $i % 4 === 0 ? 'self_pickup' : 'delivery',
                    'branch_id' => $branch->id,
                    'extra_discount' => 0,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]
            );
            if ($order->wasRecentlyCreated) {
                $prods = $products->random(min(3, $products->count()));
                foreach ($prods as $p) {
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'product_id' => $p->id,
                        'quantity' => rand(1, 3),
                        'price' => $p->price,
                        'discount_on_product' => 0,
                        'tax_amount' => 0,
                        'variant' => '',
                        'unit' => 'pc',
                        'is_stock_decreased' => 1,
                    ]);
                }
                $area = Area::where('branch_id', $branch->id)->first();
                if ($area) {
                    DB::table('order_areas')->insert([
                        'order_id' => $order->id,
                        'branch_id' => $branch->id,
                        'area_id' => $area->id,
                        'distance' => rand(1, 15),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function seedShippingAndShipments(): void
    {
        $companies = ['Aramex', 'DHL', 'البريد السريع', 'شحن محلي'];
        foreach ($companies as $i => $name) {
            ShippingCompany::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name) . '-' . $i, 'is_active' => true, 'sort_order' => $i + 1]
            );
        }
        $deliveredOrders = Order::where('order_status', 'delivered')->take(3)->get();
        $sc = ShippingCompany::first();
        if ($sc) {
            foreach ($deliveredOrders as $order) {
                if ($order->orderShipments()->count() === 0) {
                    OrderShipment::firstOrCreate(
                        ['order_id' => $order->id, 'shipping_company_id' => $sc->id],
                        [
                            'tracking_number' => 'TRK-' . $order->id . '-001',
                            'status' => 'delivered',
                            'shipped_at' => now(),
                        ]
                    );
                }
            }
        }
    }

    private function seedProductUserTypePrices(): void
    {
        $userTypes = UserType::all();
        $products = Product::take(5)->get();
        if ($userTypes->isEmpty() || $products->isEmpty()) {
            return;
        }
        foreach ($products as $p) {
            $vip = $userTypes->where('name', 'عميل مميز')->first();
            if ($vip) {
                DB::table('product_user_type_prices')->insertOrIgnore([
                    'product_id' => $p->id,
                    'user_type_id' => $vip->id,
                    'price' => $p->price * 0.9,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedLoyalty(): void
    {
        $users = User::where('email', 'like', 'customer%@test.com')->get();
        $loyaltyUser = $users->firstWhere('email', 'customer1@test.com') ?? $users->first();
        foreach ($users as $u) {
            $points = ($u->email === 'customer1@test.com') ? 500 : rand(50, 200);
            LoyaltyPoint::updateOrCreate(
                ['user_id' => $u->id],
                ['points' => $points, 'level' => 'bronze', 'total_spent' => rand(200, 800)]
            );
        }
        if ($loyaltyUser && LoyaltyPointLog::where('user_id', $loyaltyUser->id)->where('type', 'bonus')->count() === 0) {
            LoyaltyPointLog::create([
                'user_id' => $loyaltyUser->id,
                'points' => 500,
                'type' => 'bonus',
                'description' => 'نقاط تجريبية للاختبار والاستبدال',
                'order_id' => null,
            ]);
        }
    }

    /**
     * تفعيل نقاط الولاء في إعدادات الأعمال + منح عميل نقاط كافية للشراء والاستبدال.
     */
    private function seedLoyaltyEnabled(): void
    {
        DB::table('business_settings')->updateOrInsert(
            ['key' => 'loyalty_points_enabled'],
            ['value' => '1', 'updated_at' => now()]
        );
        DB::table('business_settings')->updateOrInsert(
            ['key' => 'loyalty_amount_for_one_point'],
            ['value' => '10', 'updated_at' => now()]
        );
        DB::table('business_settings')->updateOrInsert(
            ['key' => 'loyalty_points_per_amount'],
            ['value' => '1', 'updated_at' => now()]
        );
        DB::table('business_settings')->updateOrInsert(
            ['key' => 'loyalty_point_redemption_value'],
            ['value' => '0.5', 'updated_at' => now()]
        );
    }

    private function seedConversationsAndMessages(): void
    {
        $users = User::where('email', 'like', 'customer%@test.com')->take(2)->get();
        foreach ($users as $u) {
            $conv = Conversation::firstOrCreate(
                ['user_id' => $u->id],
                [
                    'message' => 'أريد الاستفسار عن المنتجات',
                    'reply' => 'مرحباً، كيف يمكننا مساعدتك؟',
                    'checked' => true,
                    'is_reply' => true,
                ]
            );
            if (Message::where('conversation_id', $conv->id)->count() === 0) {
                Message::create([
                    'conversation_id' => $conv->id,
                    'customer_id' => $u->id,
                    'message' => 'متى يصل التوصيل؟',
                ]);
                Message::create([
                    'conversation_id' => $conv->id,
                    'customer_id' => null,
                    'message' => 'خلال 24-48 ساعة',
                ]);
            }
        }
    }

    private function seedReviews(): void
    {
        $users = User::where('email', 'like', 'customer%@test.com')->get();
        $products = Product::take(6)->get();
        foreach ($products as $p) {
            if (Review::where('product_id', $p->id)->count() >= 2) {
                continue;
            }
            $user = $users->random();
            Review::firstOrCreate(
                ['product_id' => $p->id, 'user_id' => $user->id],
                [
                    'comment' => 'منتج ممتاز وجودة عالية',
                    'rating' => rand(4, 5),
                ]
            );
        }
    }

    private function seedWishlists(): void
    {
        $users = User::where('email', 'like', 'customer%@test.com')->get();
        $products = Product::pluck('id')->toArray();
        foreach ($users->take(3) as $u) {
            $selected = array_slice($products, 0, rand(2, 4));
            foreach ($selected as $pid) {
                Wishlist::firstOrCreate(
                    ['user_id' => $u->id, 'product_id' => $pid]
                );
            }
        }
    }

    private function seedContactUs(): void
    {
        if (DB::table('contact_us')->count() >= 3) {
            return;
        }
        $items = [
            ['name' => 'أحمد زائر', 'email' => 'visitor1@test.com', 'subject' => 'استفسار', 'message' => 'أريد معرفة أوقات التوصيل'],
            ['name' => 'سارة عميلة', 'email' => 'visitor2@test.com', 'subject' => 'شكوى', 'message' => 'المنتج وصل متأخراً'],
        ];
        foreach ($items as $i) {
            DB::table('contact_us')->insertOrIgnore([
                'name' => $i['name'],
                'email' => $i['email'],
                'phone' => '0599000000',
                'subject' => $i['subject'],
                'message' => $i['message'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedSocialMedias(): void
    {
        if (SocialMedia::count() >= 3) {
            return;
        }
        $items = [
            ['name' => 'Facebook', 'link' => 'https://facebook.com/elitevape', 'status' => 1],
            ['name' => 'Instagram', 'link' => 'https://instagram.com/elitevape', 'status' => 1],
            ['name' => 'WhatsApp', 'link' => 'https://wa.me/970599000000', 'status' => 1],
        ];
        foreach ($items as $i) {
            SocialMedia::firstOrCreate(
                ['name' => $i['name']],
                array_merge($i, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function seedNotifications(): void
    {
        if (Notification::count() >= 2) {
            return;
        }
        $now = now();
        DB::table('notifications')->insert([
            ['title' => 'عرض خاص', 'description' => 'خصم 20% على السوائل هذا الأسبوع', 'image' => 'def.png', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'منتجات جديدة', 'description' => 'وصلت تشكيلة جديدة من البود', 'image' => 'def.png', 'status' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    private function seedBanners(): void
    {
        if (!Schema::hasTable('banners')) {
            return;
        }
        if (DB::table('banners')->count() >= 2) {
            return;
        }
        $product = Product::first();
        $category = Category::first();
        $now = now();
        DB::table('banners')->insert([
            [
                'title' => 'بانر رئيسي',
                'image' => 'def.png',
                'product_id' => $product?->id,
                'category_id' => $category?->id,
                'status' => 1,
                'banner_type' => 'primary',
                'placement' => 'home_top',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'عرض السوائل',
                'image' => 'def.png',
                'product_id' => null,
                'category_id' => $category?->id,
                'status' => 1,
                'banner_type' => 'secondary',
                'placement' => 'home_middle',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    private function seedTags(): void
    {
        if (!Schema::hasTable('tags') || !Schema::hasTable('product_tag')) {
            return;
        }
        $tags = ['الأكثر مبيعاً', 'جديد', 'عرض خاص'];
        $tagIds = [];
        foreach ($tags as $t) {
            $slug = Str::slug($t);
            $existing = DB::table('tags')->where('slug', $slug)->first();
            if ($existing) {
                $tagIds[] = $existing->id;
            } else {
                $id = DB::table('tags')->insertGetId([
                    'name' => $t,
                    'slug' => $slug,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $tagIds[] = $id;
            }
        }
        if (empty($tagIds)) {
            return;
        }
        $products = Product::take(4)->pluck('id')->toArray();
        foreach ($products as $i => $pid) {
            $tid = $tagIds[$i % count($tagIds)];
            DB::table('product_tag')->insertOrIgnore([
                'product_id' => $pid,
                'tag_id' => $tid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedOrderStatusLogs(): void
    {
        if (!Schema::hasTable('order_status_logs')) {
            return;
        }
        $orders = Order::take(5)->get();
        foreach ($orders as $o) {
            if (DB::table('order_status_logs')->where('order_id', $o->id)->exists()) {
                continue;
            }
            DB::table('order_status_logs')->insert([
                'order_id' => $o->id,
                'old_status' => 'pending',
                'new_status' => $o->order_status,
                'changed_by_type' => 'admin',
                'changed_by_id' => 1,
                'note' => 'تحديث من لوحة التحكم',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
