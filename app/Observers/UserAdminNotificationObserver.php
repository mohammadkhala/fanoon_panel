<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * يبطئ كاش لوحة التحكم عند طلبات الموافقة على نوع مستخدم (pending_type_approval).
 */
class UserAdminNotificationObserver
{
    public function saved(User $user): void
    {
        if ($user->wasRecentlyCreated) {
            if ($user->requested_user_type_id !== null) {
                Cache::forget('admin_store_data');
            }

            return;
        }
        if ($user->wasChanged('requested_user_type_id')) {
            Cache::forget('admin_store_data');
        }
    }

    public function deleted(User $user): void
    {
        Cache::forget('admin_store_data');
    }
}
