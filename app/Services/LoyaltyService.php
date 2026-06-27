<?php

namespace App\Services;

use App\CentralLogics\Helpers;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyPointLog;
use App\Models\Order;
use App\Models\User;

class LoyaltyService
{
    public static function awardPointsForDeliveredOrder(Order $order): void
    {
        if ($order->is_guest || !$order->user_id) {
            return;
        }

        $enabled = (int) (Helpers::get_business_settings('loyalty_points_enabled') ?? 0);
        if (!$enabled) {
            return;
        }

        $pointsPerAmount = (float) (Helpers::get_business_settings('loyalty_points_per_amount') ?? 1);
        $amountForPoint = (float) (Helpers::get_business_settings('loyalty_amount_for_one_point') ?? 10);

        if ($amountForPoint <= 0) {
            return;
        }

        $pointsEarned = (int) (floor($order->order_amount / $amountForPoint) * $pointsPerAmount);
        if ($pointsEarned <= 0) {
            return;
        }

        $user = User::find($order->user_id);
        if (!$user) {
            return;
        }

        $existing = LoyaltyPointLog::where('order_id', $order->id)->where('type', 'order_reward')->exists();
        if ($existing) {
            return;
        }

        LoyaltyPoint::addPointsForOrder($user, $order->id, (float) $order->order_amount, $pointsEarned);
    }
}
