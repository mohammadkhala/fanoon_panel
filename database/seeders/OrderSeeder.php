<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds orders for testing admin orders list API and web filter.
 */
class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::first();
        $user = User::first();
        if (!$branch || !$user) {
            return;
        }

        $statuses = [
            'pending', 'confirmed', 'processing', 'out_for_delivery',
            'delivered', 'returned', 'failed', 'canceled',
        ];

        for ($i = 1; $i <= 15; $i++) {
            Order::unguarded(function () use ($i, $statuses, $branch, $user) {
                Order::create([
                    'user_id' => $user->id,
                    'is_guest' => $i % 4 === 0 ? 1 : 0,
                    'order_amount' => rand(50, 500) + (rand(0, 99) / 100),
                    'coupon_discount_amount' => 0,
                    'payment_status' => $i % 3 === 0 ? 'paid' : 'unpaid',
                    'order_status' => $statuses[array_rand($statuses)],
                    'total_tax_amount' => 0,
                    'payment_method' => 'cash_on_delivery',
                    'transaction_reference' => 'TXN-' . (1000 + $i),
                    'checked' => $i % 2,
                    'delivery_charge' => 10,
                    'order_type' => $i % 5 === 0 ? 'self_pickup' : 'delivery',
                    'branch_id' => $branch->id,
                    'extra_discount' => 0,
                ]);
            });
        }
    }
}
