# نشر Anagheem — لوحة الإدارة + API على `admin.anagheemhome.com`

## البنية على السيرفر (cPanel / مشابه)

| المسار | الدور |
|--------|--------|
| `/home/baitpait/public_html/admin` | **جذر مشروع Laravel** (يحتوي `app/`, `config/`, `artisan`, …) |
| `/home/baitpait/public_html/admin/public` | **Document root** للدومين `admin.anagheemhome.com` |

لا ترفع محتويات `public` فقط إلى الجذر العام للدومين بدون باقي المشروع؛ Laravel يحتاج المجلد الأب كاملاً.

## المتطلبات

- PHP **8.2+** (امتدادات: `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` أو ما يعادلها)
- Composer 2.x
- MySQL 8+ / MariaDB 10.6+
- تفعيل `mod_rewrite` (Apache) أو قواعد معادلة لـ Nginx

## من GitHub إلى السيرفر

1. **استنساخ** المستودع داخل `admin` (أو رفع ZIP وفكّه هناك):
   ```bash
   cd /home/baitpait/public_html
   git clone <repo-url> admin
   cd admin
   ```

2. **التبعيات (بدون حزم التطوير في الإنتاج):**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **البيئة:**
   ```bash
   cp .env.example .env
   nano .env   # أو المحرر في cPanel
   ```
   ضبط على الأقل:
   - `APP_NAME`, `APP_ENV=production`, `APP_DEBUG=false`
   - `APP_URL=https://admin.anagheemhome.com`
   - `FORCE_HTTPS=true`
   - `DB_*`
   - `CORS_ALLOWED_ORIGINS` — كل نطاقات الواجهات التي تستدعي الـ API (موقع، تطبيق ويب، منافذ تطوير إن لزم)

4. **مفتاح التطبيق وقاعدة البيانات:**
   ```bash
   php artisan key:generate
   php artisan migrate --force
   ```

5. **Passport (مصادقة API):**
   ```bash
   php artisan passport:keys --force
   ```
   إن لم تكن جداول `oauth_*` مملوءة بعملاء OAuth، نفّذ البذور المناسبة أو أنشئ عملاء Passport حسب دليل المشروع.

6. **الصور والملفات العامة:**
   ```bash
   php artisan storage:link
   ```
   تأكد أن الرابط `public/storage` يشير إلى `storage/app/public` **داخل نفس المشروع** (ليس مشروعاً آخر).

7. **الصلاحيات (Linux):**
   ```bash
   chmod -R ug+rwx storage bootstrap/cache
   chown -R baitpait:baitpait storage bootstrap/cache
   ```
   (عدّل المستخدم/المجموعة حسب الاستضافة.)

8. **الكاش بعد أي تغيير إعدادات:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## عناوين مهمة للواجهات الأخرى (API)

- **أساس الـ API:** `https://admin.anagheemhome.com/api/v1/`
- **مثال تسجيل/توكن:** حسب مسارات Passport في المشروع (غالباً `/oauth/token`).
- **توثيق OpenAPI إن وُجد:** `https://admin.anagheemhome.com/api-docs/`

في تطبيقات Flutter أو الويب: ضع **Base URL** = `https://admin.anagheemhome.com` واستخدم المسارات تحت `/api/v1/`.

## CORS

- في `.env` الإنتاج عيّن `CORS_ALLOWED_ORIGINS` بقائمة مفصولة بفواصل، مثال:
  `https://anagheemhome.com,https://www.anagheemhome.com,https://admin.anagheemhome.com`
- في `config/cors.php` يوجد نمط يسمح بأي نطاق فرعي على `*.anagheemhome.com` عبر HTTPS — مكمل للقائمة أعلاه.

## بروكسي وHTTPS

- `TrustProxies` مضبوط ليثق بالبروكسي أمام الخادم (شائع في الاستضافة المشتركة) حتى يُقرأ `X-Forwarded-Proto` بشكل صحيح.
- `FORCE_HTTPS=true` يفرض روابط `https://` في التطبيق.

## GitHub — ما يُرفع وما لا يُرفع

- **لا** ترفع ملف `.env` (موجود في `.gitignore`).
- مجلد `public/storage` عادة **غير** مُتعقّب في Git؛ بعد كل نشر نفّذ `php artisan storage:link` على السيرفر.
- بعد الاستنساخ على السيرفر: `composer install`، لا ترفع مجلد `vendor` من الجهاز إن كان `.gitignore` يستثنيه.

## DNS

- سجل **A** أو **CNAME** للدومين الفرعي `admin.anagheemhome.com` يوجّه إلى الاستضافة كما تدعم cPanel.

## تحقق سريع بعد النشر

1. فتح `https://admin.anagheemhome.com/admin/auth/login` — صفحة الدخول.
2. طلب تجريبي: `GET https://admin.anagheemhome.com/api/v1/config` (أو أول endpoint عام عندكم) — JSON وليس HTML.
3. صورة منتج: `https://admin.anagheemhome.com/storage/product/<اسم-الملف>` — تظهر بدون 404.
