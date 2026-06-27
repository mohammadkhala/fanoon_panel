<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use App\Models\GuestUser as GuestUserModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * اختبارات API قائمة طلبات العميل
 * GET /api/v1/customer/order/list
 *
 * يتحقق من أن العميل المسجّل (Bearer token) يحصل على طلباته.
 */
class CustomerOrderListApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\BaitPaitSeeder::class);
        $this->seedMinimalData();
    }

    private function seedMinimalData(): void
    {
        $this->branch = Branch::first() ?? Branch::unguarded(fn () => Branch::create([
            'name' => 'Test Branch',
            'email' => 'branch@test.com',
            'password' => bcrypt('password'),
            'status' => 1,
        ]));

        $this->user = User::unguarded(fn () => User::create([
            'f_name' => 'Test',
            'l_name' => 'Customer',
            'email' => 'customer@ordertest.com',
            'phone' => '0599111333',
            'password' => bcrypt('password'),
        ]));
    }

    /**
     * عميل مسجّل له طلبات — يجب أن ترجع الطلبات.
     */
    public function test_authenticated_user_with_orders_returns_orders(): void
    {
        Order::unguarded(fn () => Order::create([
            'user_id' => $this->user->id,
            'is_guest' => 0,
            'order_amount' => 150,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => $this->branch->id,
        ]));

        Order::unguarded(fn () => Order::create([
            'user_id' => $this->user->id,
            'is_guest' => 0,
            'order_amount' => 200,
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'order_type' => 'delivery',
            'branch_id' => $this->branch->id,
        ]));

        Passport::actingAs($this->user);

        $response = $this->getJson('/api/v1/customer/order/list');

        $response->assertStatus(200);
        $orders = $response->json();
        $this->assertIsArray($orders);
        $this->assertCount(2, $orders);
        $this->assertEquals($this->user->id, $orders[0]['user_id']);
        $this->assertEquals(0, $orders[0]['is_guest']);
    }

    /**
     * عميل مسجّل بدون طلبات — يرجع مصفوفة فارغة.
     */
    public function test_authenticated_user_without_orders_returns_empty(): void
    {
        Passport::actingAs($this->user);

        $response = $this->getJson('/api/v1/customer/order/list');

        $response->assertStatus(200);
        $this->assertSame([], $response->json());
    }

    /**
     * بدون مصادقة ولا guest-id — يرجع مصفوفة فارغة (200).
     */
    public function test_without_auth_returns_empty(): void
    {
        Config::set('guest_id', null);

        $response = $this->getJson('/api/v1/customer/order/list');

        $response->assertStatus(200);
        $this->assertSame([], $response->json());
    }

    /**
     * ضيف مع guest-id صالح — يرجع طلبات الضيف.
     */
    public function test_guest_with_valid_guest_id_returns_guest_orders(): void
    {
        $guest = GuestUserModel::unguarded(fn () => GuestUserModel::create([
            'ip_address' => '127.0.0.1',
            'fcm_token' => null,
            'language_code' => 'ar',
        ]));

        Order::unguarded(fn () => Order::create([
            'user_id' => $guest->id,
            'is_guest' => 1,
            'order_amount' => 80,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => $this->branch->id,
        ]));

        Config::set('app.guest_validate_ip', false);

        $response = $this->withHeaders(['guest-id' => (string) $guest->id])
            ->getJson('/api/v1/customer/order/list');

        $response->assertStatus(200);
        $orders = $response->json();
        $this->assertIsArray($orders);
        $this->assertCount(1, $orders);
        $this->assertEquals($guest->id, $orders[0]['user_id']);
        $this->assertEquals(1, $orders[0]['is_guest']);
    }
}
