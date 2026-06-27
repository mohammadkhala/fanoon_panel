<?php

namespace App\CentralLogics;

use App\Models\Order;

class OrderLogic
{
    public static function track_order($order_id)
    {
        return Order::with(['details', 'statusLogs'])->where(['id' => $order_id])->first();
    }

}
