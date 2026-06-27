<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\OrderStatusLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusLogServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_creates_record(): void
    {
        $order = Order::create([
            'user_id' => null,
            'is_guest' => 1,
            'order_amount' => 50,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => 1,
        ]);

        OrderStatusLogService::log($order, 'pending', 'confirmed');

        $this->assertCount(1, OrderStatusLog::where('order_id', $order->id)->get());
        $log = OrderStatusLog::first();
        $this->assertEquals('pending', $log->old_status);
        $this->assertEquals('confirmed', $log->new_status);
    }

    public function test_log_accepts_note(): void
    {
        $order = Order::create([
            'user_id' => null,
            'is_guest' => 1,
            'order_amount' => 50,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'order_type' => 'delivery',
            'branch_id' => 1,
        ]);

        OrderStatusLogService::log($order, 'pending', 'confirmed', 'Manual update');

        $log = OrderStatusLog::first();
        $this->assertEquals('Manual update', $log->note);
    }
}
