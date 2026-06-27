<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestAdminLoginCommand extends Command
{
    protected $signature = 'test:admin-login
                            {--email= : Admin email (default: admin@admin.com)}
                            {--password= : Admin password (default: 12345678)}
                            {--url=http://127.0.0.1:8000 : Base URL}
                            {--debug : عرض تفاصيل إضافية}';

    protected $description = 'اختبار تسجيل دخول Admin عبر HTTP';

    public function handle(): int
    {
        $baseUrl = rtrim($this->option('url'), '/');
        $email = $this->option('email') ?: 'admin@admin.com';
        $password = $this->option('password') ?: '12345678';
        $verbose = $this->option('debug');

        $this->info('جاري اختبار تسجيل الدخول...');
        $this->line("  الرابط: {$baseUrl}/admin/auth/login");
        $this->line("  البريد: {$email}");

        $jar = new \GuzzleHttp\Cookie\CookieJar();

        // 1. جلب صفحة تسجيل الدخول
        $loginPage = Http::withOptions([
            'cookies' => $jar,
            'allow_redirects' => false,
        ])->get("{$baseUrl}/admin/auth/login");

        if (!$loginPage->successful()) {
            $this->error("فشل جلب صفحة الدخول: HTTP {$loginPage->status()}");
            return 1;
        }

        // 2. استخراج CSRF token
        $csrfToken = $this->extractCsrfToken($loginPage->body());
        if (!$csrfToken) {
            $this->warn('لم يتم العثور على CSRF token - المحاولة بدونه');
        }
        if ($verbose) {
            $this->line('  CSRF: ' . ($csrfToken ? substr($csrfToken, 0, 20) . '...' : 'لا يوجد'));
        }

        // 3. إرسال بيانات الدخول
        $postData = [
            'email' => $email,
            'password' => $password,
            '_token' => $csrfToken ?? '',
        ];

        $loginResponse = Http::withOptions([
            'cookies' => $jar,
            'allow_redirects' => true,
        ])->asForm()->post("{$baseUrl}/admin/auth/login", $postData);

        // 4. التحقق من النتيجة
        $finalUrl = $loginResponse->effectiveUri()->__toString();
        $isDashboard = str_contains($finalUrl, 'admin') && !str_contains($finalUrl, 'login');
        $body = $loginResponse->body();

        if ($verbose) {
            $this->line("  الاستجابة: HTTP {$loginResponse->status()}");
            $this->line("  الرابط النهائي: {$finalUrl}");
        }

        // نجاح: تم التوجيه إلى لوحة التحكم (حتى لو الصفحة أعطت 500 لاحقاً)
        if ($isDashboard) {
            $this->info('✅ تسجيل الدخول ناجح');
            $this->line("  تم التوجيه إلى: {$finalUrl}");
            if ($loginResponse->status() === 500) {
                $this->warn('  ملاحظة: صفحة لوحة التحكم قد تعرض خطأ 500 - راجع السجلات');
            }
            return 0;
        }

        if (str_contains($body, 'Credentials does not match') ||
            str_contains($body, 'credentials') ||
            str_contains($body, 'match') ||
            str_contains($body, 'credentials') ||
            str_contains($body, 'البريد') ||
            str_contains($body, 'كلمة المرور')) {
            $this->error('❌ فشل: البريد أو كلمة المرور غير صحيحة');
            return 1;
        }

        if (str_contains($body, 'Captcha') || str_contains($body, 'captcha') || str_contains($body, 'captcha')) {
            $this->error('❌ فشل: التحقق (Captcha) مطلوب - تعطيله من الإعدادات للاختبار');
            return 1;
        }

        $this->error("❌ فشل تسجيل الدخول (HTTP {$loginResponse->status()})");
        $this->line('استخدم: php artisan test:admin-login --email=your@email.com --password=yourpass');
        return 1;
    }

    private function extractCsrfToken(string $html): ?string
    {
        if (preg_match('/name="_token"\s+value="([^"]+)"/', $html, $m)) {
            return $m[1];
        }
        if (preg_match('/content="([^"]+)"\s+name="csrf-token"/', $html, $m)) {
            return $m[1];
        }
        if (preg_match('/"csrfToken":"([^"]+)"/', $html, $m)) {
            return $m[1];
        }
        return null;
    }
}
