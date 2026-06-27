<?php

namespace App\Console\Commands;

use App\Models\ContactUs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * لاختبار يدوي: إشعار رسائل تواصل معنا في /admin ومودال get-store-data.
 */
class InsertTestContactUsForAdminNotificationCommand extends Command
{
    protected $signature = 'dev:admin-notification-test-contact-us
                            {--count=2 : عدد الرسائل التجريبية}
                            {--force : السماح في بيئة production}';

    protected $description = 'إدراج رسائل تواصل تجريبية غير مقروءة لاختبار /admin/contact-us والإشعار';

    public function handle(): int
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('بيئة الإنتاج: للتنفيذ أضف --force إن كنت متأكداً.');

            return self::FAILURE;
        }

        $count = max(1, min(50, (int) $this->option('count')));
        $stamp = now()->format('Ymd-His');

        for ($i = 1; $i <= $count; $i++) {
            ContactUs::query()->create([
                'name' => 'اختبار تواصل '.$stamp.'-'.$i,
                'email' => 'contact-test-'.$stamp.'-'.$i.'@example.test',
                'phone' => '0590000'.str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'subject' => 'رسالة تجريبية — التحقق من الإشعار',
                'message' => 'رسالة تجريبية رقم '.$i.' لاختبار صفحة تواصل معنا ومودال الإشعار في لوحة التحكم.',
            ]);
        }

        Cache::forget('admin_store_data');

        $unread = ContactUs::unread()->count();
        $this->info("تم إنشاء {$count} رسالة(ات) تواصل غير مقروءة.");
        $this->line("— إجمالي غير المقروء في قاعدة البيانات الآن: {$unread}");
        $this->line('— افتح /admin/contact-us للتحقق من القائمة.');
        $this->line('— أولوية المودال: طلب جديد → موافقة نوع → تواصل. إن وُجدت طلبات غير مُراجَعة سيظهر مودال الطلب أولاً؛ استخدم «تجاهل الآن» للطلب أو عالج الطلبات ليظهر مودال التواصل، أو علّم الطلبات مقروءة.');

        return self::SUCCESS;
    }
}
