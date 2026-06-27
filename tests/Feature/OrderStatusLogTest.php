<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\User;
use App\Services\OrderStatusLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OrderStatusLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimalData();
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
        if (!User::first()) {
            $u = new User();
            $u->f_name = 'Test';
            $u->l_name = 'User';
            $u->email = 'user@test.com';
            $u->phone = '0599999998';
            $u->password = bcrypt('password');
            $u->save();
        }
    }

    public function test_order_status_log_service_creates_log(): void
    {
        $branch = Branch::first();
        $user = User::first();
        $order = Order::create([
            'user_id' => $user->id,
            'is_guest' => 0,
            'order_amount' => 100,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => $branch->id,
        ]);

        OrderStatusLogService::log($order, 'pending', 'confirmed');

        $this->assertDatabaseHas('order_status_logs', [
            'order_id' => $order->id,
            'old_status' => 'pending',
            'new_status' => 'confirmed',
        ]);
    }

    public function test_order_status_log_with_admin(): void
    {
        $admin = new Admin();
        $admin->f_name = 'Test';
        $admin->l_name = 'Admin';
        $admin->email = 'admin2@test.com';
        $admin->password = Hash::make('password');
        $admin->save();

        $branch = Branch::first();
        $user = User::first();
        $order = Order::create([
            'user_id' => $user->id,
            'is_guest' => 0,
            'order_amount' => 100,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => $branch->id,
        ]);

        $this->actingAs($admin, 'admin');
        OrderStatusLogService::log($order, 'pending', 'confirmed');

        $log = OrderStatusLog::where('order_id', $order->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals(Admin::class, $log->changed_by_type);
        $this->assertEquals($admin->id, $log->changed_by_id);
    }
}
