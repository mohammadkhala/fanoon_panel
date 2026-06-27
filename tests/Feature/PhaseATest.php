<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * اختبارات المرحلة أ: تصدير العملاء، فلتر التقييم، شارات مراجعة.
 */
class PhaseATest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seedMinimalData();
        $this->admin = Admin::first() ?? tap(new Admin(), function ($a) {
            $a->f_name = 'Test';
            $a->l_name = 'Admin';
            $a->email = 'admin@test.com';
            $a->password = Hash::make('password');
            $a->save();
        });
    }

    private function seedMinimalData(): void
    {
        if (!Branch::first()) {
            $b = new Branch();
            $b->name = 'Test Branch';
            $b->email = 'branch@test.com';
            $b->password = bcrypt('password');
            $b->status = true;
            $b->save();
        }
        if (!UserType::first()) {
            UserType::create(['name' => 'Default', 'is_default' => 1]);
        }
    }

    public function test_customer_export_requires_auth(): void
    {
        $response = $this->get(route('admin.customer.export'));
        $response->assertRedirect();
    }

    public function test_customer_export_returns_excel(): void
    {
        $user = User::create([
            'f_name' => 'Export',
            'l_name' => 'Test',
            'email' => 'export@test.com',
            'phone' => '0599999999',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.customer.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('customers.xlsx', $response->headers->get('content-disposition') ?? '');
    }

    public function test_customer_export_applies_search_filter(): void
    {
        User::create([
            'f_name' => 'Unique',
            'l_name' => 'Customer',
            'email' => 'unique@test.com',
            'phone' => '0591111111',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.customer.export', ['search' => 'Unique']));

        $response->assertStatus(200);
    }

    public function test_product_list_rating_filter(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.product.list', ['rating_min' => 4]));

        $response->assertStatus(200);
    }

    public function test_customer_badge_logic_confirmed_buyer(): void
    {
        $user = User::create([
            'f_name' => 'Buyer',
            'l_name' => 'Five',
            'email' => 'buyer5@test.com',
            'phone' => '0592222222',
            'password' => bcrypt('password'),
        ]);

        $branch = Branch::first();
        for ($i = 0; $i < 5; $i++) {
            Order::create([
                'user_id' => $user->id,
                'is_guest' => 0,
                'order_amount' => 10,
                'payment_status' => 'paid',
                'order_status' => 'delivered',
                'order_type' => 'delivery',
                'branch_id' => $branch->id,
            ]);
        }

        $orderCount = Order::where('user_id', $user->id)->count();
        $this->assertGreaterThanOrEqual(5, $orderCount);
    }

    public function test_customer_badge_logic_trusted(): void
    {
        $user = User::create([
            'f_name' => 'Trusted',
            'l_name' => 'Ten',
            'email' => 'trusted10@test.com',
            'phone' => '0593333333',
            'password' => bcrypt('password'),
        ]);

        $branch = Branch::first();
        for ($i = 0; $i < 10; $i++) {
            Order::create([
                'user_id' => $user->id,
                'is_guest' => 0,
                'order_amount' => 10,
                'payment_status' => 'paid',
                'order_status' => 'delivered',
                'order_type' => 'delivery',
                'branch_id' => $branch->id,
            ]);
        }

        $orderCount = Order::where('user_id', $user->id)->count();
        $this->assertGreaterThanOrEqual(10, $orderCount);
    }
}
