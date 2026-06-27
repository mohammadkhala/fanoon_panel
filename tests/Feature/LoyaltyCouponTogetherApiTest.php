<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\LoyaltyPoint;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * API tests for loyalty_and_coupon_together setting.
 */
class LoyaltyCouponTogetherApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected CustomerAddress $address;
    protected Coupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\BaitPaitSeeder::class);
        $this->seedMinimalData();
    }

    private function seedMinimalData(): void
    {
        $branch = Branch::first();
        if (!$branch) {
            $this->markTestSkipped('Branch required');
        }

        $this->user = User::unguarded(fn () => User::create([
            'f_name' => 'Test',
            'l_name' => 'Customer',
            'email' => 'customer@loyaltytest.com',
            'phone' => '0599111222',
            'password' => bcrypt('password'),
        ]));

        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Test Cat',
            'parent_id' => 0,
            'position' => 0,
            'status' => 1,
            'image' => 'def.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $now = now();
        DB::table('products')->insert([
            'name' => 'Test Product',
            'category_ids' => json_encode([['id' => (string) $categoryId, 'position' => 1]]),
            'image' => json_encode(['def.png']),
            'price' => 100,
            'discount' => 0,
            'discount_type' => 'amount',
            'total_stock' => 50,
            'variations' => json_encode([['type' => 'default', 'price' => 0, 'stock' => 50]]),
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->product = Product::first();

        $this->address = CustomerAddress::unguarded(fn () => CustomerAddress::create([
            'user_id' => $this->user->id,
            'contact_person_name' => 'Test',
            'contact_person_number' => '0599111222',
            'address_type' => 'Home',
            'address' => 'Test Address',
            'city' => 'Test City',
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        $this->coupon = Coupon::unguarded(fn () => Coupon::create([
            'title' => 'Test Coupon',
            'code' => 'TEST10',
            'discount' => 10,
            'discount_type' => 'percent',
            'min_purchase' => 50,
            'max_discount' => 20,
            'limit' => 10,
            'coupon_type' => 'default',
            'start_date' => now()->format('Y-m-d'),
            'expire_date' => now()->addMonths(1)->format('Y-m-d'),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        LoyaltyPoint::unguarded(fn () => LoyaltyPoint::create([
            'user_id' => $this->user->id,
            'points' => 100,
            'level' => 'bronze',
            'total_spent' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        DB::table('business_settings')->updateOrInsert(
            ['key' => 'loyalty_points_enabled'],
            ['value' => '1', 'updated_at' => now()]
        );
        DB::table('business_settings')->updateOrInsert(
            ['key' => 'loyalty_and_coupon_together'],
            ['value' => '1', 'updated_at' => now()]
        );
    }

    private function orderPayload(): array
    {
        return [
            'cart' => [
                ['product_id' => $this->product->id, 'quantity' => 1, 'variation' => []],
            ],
            'payment_method' => 'cash_on_delivery',
            'payment_platform' => 'app',
            'callback' => 'https://example.com/callback',
            'order_type' => 'delivery',
            'delivery_address_id' => $this->address->id,
            'customer_id' => $this->user->id,
            'bring_change_amount' => 0,
            'is_guest' => 0,
        ];
    }

    /**
     * Config API returns loyalty_and_coupon_together.
     */
    public function test_config_returns_loyalty_and_coupon_together(): void
    {
        $response = $this->getJson('/api/v1/config');

        $response->assertStatus(200)
            ->assertJsonStructure(['loyalty_and_coupon_together'])
            ->assertJsonPath('loyalty_and_coupon_together', 1);
    }

    /**
     * When loyalty_and_coupon_together=0, place order with both coupon and loyalty returns 422.
     */
    public function test_place_order_rejects_both_when_setting_disabled(): void
    {
        DB::table('business_settings')
            ->where('key', 'loyalty_and_coupon_together')
            ->update(['value' => '0', 'updated_at' => now()]);

        Passport::actingAs($this->user);

        $payload = $this->orderPayload();
        $payload['coupon_code'] = 'TEST10';
        $payload['loyalty_points_used'] = 10;

        $response = $this->postJson('/api/v1/customer/order/place', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('errors.0.code', 'loyalty_points_used');
    }

    /**
     * When loyalty_and_coupon_together=1, place order with both does NOT return loyalty_coupon_only_one error.
     */
    public function test_place_order_allows_both_when_setting_enabled(): void
    {
        DB::table('business_settings')
            ->where('key', 'loyalty_and_coupon_together')
            ->update(['value' => '1', 'updated_at' => now()]);

        Passport::actingAs($this->user);

        $payload = $this->orderPayload();
        $payload['coupon_code'] = 'TEST10';
        $payload['loyalty_points_used'] = 10;

        $response = $this->postJson('/api/v1/customer/order/place', $payload);

        if ($response->status() === 422) {
            $errors = $response->json('errors', []);
            $loyaltyError = collect($errors)->firstWhere('code', 'loyalty_points_used');
            $this->assertNull($loyaltyError, 'Should not get loyalty_coupon_only_one when setting is enabled');
        } else {
            $response->assertSuccessful();
        }
    }
}
