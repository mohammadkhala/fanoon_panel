<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusLog;

class OrderStatusLogService
{
    /**
     * تسجيل تغيير حالة الطلب.
     *
     * @param Order $order الطلب
     * @param string $oldStatus الحالة السابقة
     * @param string $newStatus الحالة الجديدة
     * @param string|null $note ملاحظة اختيارية
     */
    public static function log(Order $order, string $oldStatus, string $newStatus, ?string $note = null): void
    {
        $changedByType = null;
        $changedById = null;

        if (auth('admin')->check()) {
            $changedByType = \App\Models\Admin::class;
            $changedById = auth('admin')->id();
        } elseif (auth('branch')->check()) {
            $changedByType = \App\Models\Branch::class;
            $changedById = auth('branch')->id();
        } else {
            $changedByType = 'system';
        }

        OrderStatusLog::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_type' => $changedByType,
            'changed_by_id' => $changedById,
            'note' => $note,
        ]);
    }
}
