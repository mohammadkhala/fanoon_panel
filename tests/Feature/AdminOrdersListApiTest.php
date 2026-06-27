<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests for admin orders list API (JSON response for web filter app).
 */
class AdminOrdersListApiTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::unguarded(fn () => Admin::create([
            'f_name' => 'Test',
            'l_name' => 'Admin',
            'phone' => '0599999999',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]));
    }

    /**
     * Test unauthenticated request returns 401 JSON.
     */
    public function test_orders_list_api_returns_401_when_unauthenticated(): void
    {
        $response = $this->getJson('/admin/orders/list/all');

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    /**
     * Test authenticated admin gets JSON orders list.
     */
    public function test_orders_list_api_returns_json_when_authenticated(): void
    {
        $this->seedOrders();

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/admin/orders/list/all');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'order_status',
                        'payment_status',
                        'order_amount',
                        'order_type',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
                'filters',
            ])
            ->assertJson(['success' => true]);
    }

    /**
     * Test orders list API with status filter.
     */
    public function test_orders_list_api_filters_by_status(): void
    {
        $this->seedOrders();

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/admin/orders/list/delivered');

        $response->assertStatus(200);
        $data = $response->json('data');
        foreach ($data as $order) {
            $this->assertSame('delivered', $order['order_status']);
        }
    }

    /**
     * Test orders list API with search.
     */
    public function test_orders_list_api_filters_by_search(): void
    {
        $this->seedOrders();
        $order = Order::notPos()->first();
        if (!$order) {
            $this->markTestSkipped('No orders to search');
        }

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/admin/orders/list/all?search=' . $order->id);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertGreaterThanOrEqual(1, count($data));
        $this->assertSame((string) $order->id, (string) $data[0]['id']);
    }

    /**
     * Test orders list API with date range.
     */
    public function test_orders_list_api_filters_by_date_range(): void
    {
        $this->seedOrders();

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/admin/orders/list/all?start_date=2020-01-01&end_date=2030-12-31');

        $response->assertStatus(200)
            ->assertJsonPath('filters.start_date', '2020-01-01')
            ->assertJsonPath('filters.end_date', '2030-12-31');
    }

    /**
     * Test orders list API pagination.
     */
    public function test_orders_list_api_pagination(): void
    {
        $this->seedOrders();

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson('/admin/orders/list/all?per_page=5&page=1');

        $response->assertStatus(200);
        $this->assertLessThanOrEqual(5, count($response->json('data')));
        $this->assertSame(5, $response->json('meta.per_page'));
    }

    private function seedOrders(): void
    {
        $branch = Branch::first();
        $user = User::first();
        if (!$branch) {
            Branch::unguarded(fn () => Branch::create([
                'name' => 'Test Branch',
                'email' => 'branch@test.com',
                'password' => bcrypt('password'),
                'status' => 1,
            ]));
            $branch = Branch::first();
        }
        if (!$user) {
            User::unguarded(fn () => User::create([
                'f_name' => 'Test',
                'l_name' => 'User',
                'email' => 'user@test.com',
                'phone' => '0599999998',
                'password' => bcrypt('password'),
            ]));
            $user = User::first();
        }

        $statuses = ['pending', 'confirmed', 'delivered', 'canceled'];
        for ($i = 0; $i < 5; $i++) {
            Order::unguarded(fn () => Order::create([
                'user_id' => $user->id,
                'is_guest' => 0,
                'order_amount' => 100,
                'payment_status' => 'unpaid',
                'order_status' => $statuses[$i % 4],
                'order_type' => 'delivery',
                'branch_id' => $branch->id,
            ]));
        }
    }
}
