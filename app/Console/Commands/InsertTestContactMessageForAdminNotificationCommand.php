<?php

namespace App\Console\Commands;

use App\Models\ContactUs;
use Illuminate\Console\Command;

/**
 * لاختبار يدوي: نافذة «رسالة تواصل جديدة» + الصوت في /admin.
 */
class InsertTestContactMessageForAdminNotificationCommand extends Command
{
    protected $signature = 'dev:admin-notification-test-contact
                            {--subject=اختبار إشعار : عنوان الرسالة}
                            {--force : السماح في بيئة production}';

    protected $description = 'إدراج رسالة تواصل معنا غير مقروءة لاختبار الإشعار في لوحة التحكم';

    public function handle(): int
    {
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('بيئة الإنتاج: للتنفيذ أضف --force إن كنت متأكداً.');

            return self::FAILURE;
        }

        $subject = (string) $this->option('subject');

        $row = ContactUs::query()->create([
            'name' => 'اختبار إشعار',
            'email' => 'notify-test@example.test',
            'phone' => null,
            'subject' => $subject,
            'message' => 'رسالة تجريبية من أمر Artisan لاختبار البوب أب والصوت في لوحة التحكم.',
        ]);

        $this->info('تم إنشاء رسالة تواصل رقم: '.$row->id);
        $this->line('— تأكد ألا يوجد طلب جديد غير مُراجع؛ وإلا سيظهر إشعار الطلب أولاً (الأولوية في الواجهة).');
        $this->line('— افتح /admin وحدّث الصفحة أو انتظر الاستطلاع، وانقر مرة لتفعيل الصوت.');

        return self::SUCCESS;
    }
}
