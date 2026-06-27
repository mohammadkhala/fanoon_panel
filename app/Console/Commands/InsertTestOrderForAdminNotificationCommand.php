<?php

namespace App\Console\Commands;

use App\CentralLogics\Helpers;
use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * لاختبار يدوي: إشعار الطلب الجديد + الصوت في /admin (واستطلاع get-store-data).
 */
class InsertTestOrderForAdminNotificationCommand extends Command
{
    protected $signature = 'dev:admin-notification-test-order
                            {--user= : معرّف المستخدم (افتراضي: أول مستخدم)}
                            {--branch= : معرّف الفرع (افتراضي: أول فرع موجود)}
                            {--amount=99 : قيمة الطلب}
                            {--force : السماح في بيئة production}';

    protected $description = 'إدراج طلب تجريبي غير مُراجع (checked=0) لاختبار إشعار لوحة التحكم';

    public function handle(): int
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('بيئة الإنتاج: للتنفيذ أضف --force إن كنت متأكداً.');

            return self::FAILURE;
        }

        $branchOption = $this->option('branch');
        if ($branchOption !== null && $branchOption !== '') {
            $branchId = (int) $branchOption;
        } else {
            $branchId = (int) (Branch::query()->orderBy('id')->value('id') ?: Helpers::getDefaultBranchId());
        }

        if (!Branch::query()->whereKey($branchId)->exists()) {
            $this->error("لا يوجد فرع بالمعرّف {$branchId}.");

            return self::FAILURE;
        }

        $userOption = $this->option('user');
        if ($userOption !== null && $userOption !== '') {
            $userId = (int) $userOption;
        } else {
            $userId = (int) (User::query()->orderBy('id')->value('id') ?: 0);
        }

        if ($userId < 1 || !User::query()->whereKey($userId)->exists()) {
            $this->error('لا يوجد مستخدم صالح. أنشئ عميلاً من لوحة التحكم أو مرّر --user=');

            return self::FAILURE;
        }

        $amount = (float) $this->option('amount');

        $order = Order::query()->create([
            'user_id' => $userId,
            'is_guest' => 0,
            'order_amount' => $amount,
            'payment_status' => 'unpaid',
            'order_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'order_type' => 'delivery',
            'branch_id' => $branchId,
            'checked' => 0,
            'delivery_charge' => 0,
        ]);

        Cache::forget('admin_store_data');
        Cache::forget('branch_store_data_'.$branchId);

        $this->info("تم إنشاء طلب تجريبي: {$order->id}");
        $fresh = Order::notPos()->where('checked', 0)->count();
        $this->line("— عدد الطلبات غير المُراجَعة (غير POS) في قاعدة البيانات الآن: {$fresh}");
        $this->line('— افتح لوحة التحكم /admin وانتظر حتى 10 ثوانٍ أو حدّث الصفحة.');
        $this->line('— إن لم يظهر المودال: في أدوات المطوّر → Application → Session Storage احذف المفتاح elite_admin_snooze_order (كان يُخفِي التنبيه إذا بقي العدد كما كان عند التجاهل).');
        $this->line('— انقر مرة داخل الصفحة لتفعيل الصوت في المتصفح.');
        $this->line('— لإزالة التنبيه: زر «تجاهل الآن» في المودال، أو فتح صفحة تفاصيل الطلب (تُعلَّم كمقروء)، أو تعيين checked=1.');

        return self::SUCCESS;
    }
}
