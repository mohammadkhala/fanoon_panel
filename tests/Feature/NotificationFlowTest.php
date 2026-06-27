<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\ContactUs;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * اختبار تدفق إشعارات الطلب الجديد ورسالة تواصل معنا
 *
 * يتحقق من:
 * - استجابة get-store-data
 * - عد الطلبات غير المُراجعة (checked=0, order_type != pos)
 * - عد رسائل تواصل معنا غير المقروءة
 * - إبطال الكاش عند إنشاء طلب
 */
class NotificationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected Branch $branch;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->admin = Admin::unguarded(fn () => Admin::create([
            'f_name' => 'Test',
            'l_name' => 'Admin',
            'phone' => '0599999999',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]));
        $this->branch = Branch::unguarded(fn () => Branch::create([
            'name' => 'Test Branch',
            'email' => 'branch@test.com',
            'password' => Hash::make('password'),
        ]));
        $this->user = User::unguarded(fn () => User::create([
            'f_name' => 'Test',
            'l_name' => 'User',
            'phone' => '0598888888',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
        ]));
    }

    /** @test */
    public function get_store_data_returns_json_with_expected_structure(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.get-store-data'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'new_order',
                    'pending_type_approval',
                    'new_contact_us',
                ],
            ])
            ->assertJson(['success' => 1]);

        $data = $response->json('data');
        $this->assertIsInt($data['new_order']);
        $this->assertIsInt($data['pending_type_approval']);
        $this->assertIsInt($data['new_contact_us']);
    }

    /** @test */
    public function new_order_increments_new_order_count(): void
    {
        Order::create([
            'user_id' => $this->user->id,
            'is_guest' => 0,
            'order_amount' => 100,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => $this->branch->id,
            'checked' => 0,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.get-store-data'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('data.new_order'));
    }

    /** @test */
    public function pos_orders_are_excluded_from_new_order_count(): void
    {
        Order::create([
            'user_id' => $this->user->id,
            'is_guest' => 0,
            'order_amount' => 100,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'pos',
            'branch_id' => $this->branch->id,
            'checked' => 0,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.get-store-data'));

        $response->assertOk();
        $this->assertEquals(0, $response->json('data.new_order'));
    }

    /** @test */
    public function unread_contact_us_increments_new_contact_us_count(): void
    {
        ContactUs::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'phone' => '123',
            'subject' => 'Test',
            'message' => 'Test message',
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.get-store-data'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('data.new_contact_us'));
    }

    /** @test */
    public function contact_us_observer_clears_store_data_cache_after_create(): void
    {
        $this->actingAs($this->admin, 'admin')->getJson(route('admin.get-store-data'));
        $this->assertTrue(Cache::has('admin_store_data'));

        ContactUs::create([
            'name' => 'Cache',
            'email' => 'cache@test.com',
            'phone' => '1',
            'subject' => 'S',
            'message' => 'M',
            'read_at' => null,
        ]);

        $this->assertFalse(Cache::has('admin_store_data'));
    }

    /** @test */
    public function cache_is_used_for_store_data(): void
    {
        $this->assertFalse(Cache::has('admin_store_data'));

        $this->actingAs($this->admin, 'admin')->getJson(route('admin.get-store-data'));

        $this->assertTrue(Cache::has('admin_store_data'));
    }

    /** @test */
    public function ignore_check_order_clears_cache(): void
    {
        Cache::put('admin_store_data', ['new_order' => 5], 60);

        $this->actingAs($this->admin, 'admin')->get(route('admin.ignore-check-order'));

        $this->assertFalse(Cache::has('admin_store_data'));
    }

    /** @test */
    public function opening_order_list_does_not_mark_all_orders_checked(): void
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'is_guest' => 0,
            'order_amount' => 50,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => $this->branch->id,
            'checked' => 0,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.list', ['status' => 'all']));

        $this->assertSame(0, (int) $order->fresh()->checked);
    }

    /** @test */
    public function get_store_data_reports_new_order_and_new_contact_counts_together(): void
    {
        Order::create([
            'user_id' => $this->user->id,
            'is_guest' => 0,
            'order_amount' => 10,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => $this->branch->id,
            'checked' => 0,
        ]);
        ContactUs::create([
            'name' => 'Both',
            'email' => 'both@test.com',
            'phone' => '1',
            'subject' => 'S',
            'message' => 'M',
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.get-store-data'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('data.new_order'));
        $this->assertGreaterThanOrEqual(1, $response->json('data.new_contact_us'));
    }
}
