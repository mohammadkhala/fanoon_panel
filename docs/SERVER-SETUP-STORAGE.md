# إعداد مجلدات التخزين على السيرفر

إذا ظهر خطأ **"View path not found"** أو **500 Internal Server Error**، غالباً مجلدات `storage` ناقصة على السيرفر. نفّذ الأوامر التالية من **داخل مجلد المشروع** (حيث يوجد `artisan`):

---

## 1) إنشاء المجلدات المطلوبة

```bash
cd admin.elitevape.online
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/logs
touch storage/logs/laravel.log
```

---

## 2) صلاحيات الكتابة

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 3) مسح الكاش ثم التحسين

```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize
```

---

## 4) إذا استمر خطأ 500

- افتح ملف **`storage/logs/laravel.log`** من الأسفل واقرأ آخر رسالة خطأ.
- أو ضع في **`.env`** مؤقتاً: **`APP_DEBUG=true`** ثم حدّث الصفحة لرؤية تفاصيل الخطأ، ثم أرجعها إلى **`false`** بعد الحل.

---

*نفّذ الخطوة 1 ثم 2 ثم 3 بالترتيب.*
