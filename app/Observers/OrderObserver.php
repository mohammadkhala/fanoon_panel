<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    /**
     * لوحة الأدمن/الفرع تستطلع get-store-data مع كاش؛ أي حفظ للطلب يجب أن يُبطِل العدادات.
     */
    public function saved(Order $order): void
    {
        Cache::forget('admin_store_data');
        if ($order->branch_id) {
            Cache::forget('branch_store_data_'.$order->branch_id);
        }
    }

    /**
     * Handle the Order "created" event.
     * New order affects top_customer (order count per user).
     */
    public function created(Order $order): void
    {
        Cache::forget('admin_dashboard_top_customer');
    }

    /**
     * Handle the Order "updated" event.
     * When order becomes delivered, top_sell (product quantities) changes.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('order_status') && $order->order_status === 'delivered') {
            Cache::forget('admin_dashboard_top_sell');
        }
    }
}
