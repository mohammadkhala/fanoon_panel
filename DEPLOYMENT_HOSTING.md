# دليل رفع المشروع على الاستضافة المشتركة - elitevape.online

## معلومات الاستضافة

| البند | القيمة |
|-------|--------|
| النطاق | elitevape.online |
| قاعدة البيانات | MySQL |
| اسم قاعدة البيانات | anagmgxt_elitevape |
| اسم المستخدم | anagmgxt_elitevape |
| كلمة المرور | 56_qc%N7mp[0 |

> **ملاحظة:** ضع كلمة مرور قاعدة البيانات في ملف `.env` عند الرفع. استخدم علامات تنصيص إذا احتوت على رموز خاصة: `DB_PASSWORD="56_qc%N7mp[0"`

---

## إنشاء حزمة الرفع (قبل الرفع)

من مجلد المشروع نفّذ:

```bash
./create_deploy_package.sh
```

سيُنشئ مجلد `elitevape_deploy` يحتوي على:
- جميع ملفات المشروع (بما فيها `vendor` و `storage`)
- صور المنتجات والتصنيفات والشعار من `storage/app/public`
- نسخة من `public/storage` للوصول المباشر بدون أوامر شيل
- `elitevape_database_import.sql` و `.env.hosting.example`

**لتصدير قاعدة البيانات الحالية (مع المنتجات والتصنيفات):**
```bash
EXPORT_DB=1 ./create_deploy_package.sh
```

**لإنشاء أرشيف ZIP للرفع:**
```bash
cd elitevapeDB && zip -r elitevape_deploy.zip elitevape_deploy
```

---

## خطوات الرفع

### 1. رفع الملفات

- ارفع محتويات مجلد `elitevape_deploy` (أو الملف `elitevape_deploy.zip` بعد فك الضغط) إلى المجلد الرئيسي على الاستضافة (مثلاً `public_html` أو `elitevape`)
- **مهم:** تأكد أن مجلد `public` يحتوي على `index.php` و `.htaccess`

### 2. ضبط جذر الموقع (Document Root)

في لوحة التحكم (cPanel / Hostinger):

- **الطريقة المفضلة:** اجعل جذر الموقع يشير إلى مجلد `public` داخل المشروع  
  مثال: إذا كان المشروع في `public_html/elitevapeDB` فاجعل الجذر: `public_html/elitevapeDB/public`

- **بديل:** إذا لم تستطع تغيير الجذر، انقل محتويات مجلد `public` إلى الجذر وعدّل المسارات في `index.php` حسب الحاجة.

### 3. إنشاء ملف .env

```bash
# انسخ القالب
cp .env.hosting.example .env

# أو أنشئ .env يدوياً وانسخ المحتوى من .env.hosting.example
```

**توليد مفتاح التطبيق:**
```bash
php artisan key:generate
```

### 4. استيراد قاعدة البيانات (بدون شيل)

**لا تحتاج أوامر الطرفية.** استخدم phpMyAdmin:

1. أنشئ قاعدة بيانات باسم: `anagmgxt_elitevape` (إن لم تكن موجودة)
2. أنشئ مستخدماً: `anagmgxt_elitevape` بكلمة المرور: `56_qc%N7mp[0`
3. امنح المستخدم صلاحيات كاملة على قاعدة البيانات
4. افتح phpMyAdmin → اختر قاعدة البيانات `anagmgxt_elitevape`
5. اذهب إلى تبويب **استيراد (Import)**
6. اختر الملف: `elitevape_database_import.sql` من مجلد المشروع
7. اضغط **تنفيذ (Go)**

### 5. إنشاء ملف .env يدوياً

أنشئ ملف `.env` في جذر المشروع وانسخ المحتوى من `.env.hosting.example` ثم عدّل:
- `APP_KEY=` — ولّد مفتاحاً من: https://generate-random.org/laravel-key-generator أو استخدم أحد المفاتيح الموجودة في `config/app.php`
- `DB_PASSWORD="56_qc%N7mp[0"`

### 6. رابط التخزين (بدون شيل)

إذا لم يكن لديك وصول للشيل، أنشئ رابطاً رمزياً يدوياً:
- في الاستضافة: `public/storage` → يجب أن يشير إلى `../storage/app/public`
- أو انسخ `storage/app/public` إلى `public/storage`

### 7. مسح الكاش (اختياري)

في المتصفح: `https://elitevape.online/` — يجب أن يعمل الموقع مباشرة بعد استيراد قاعدة البيانات وضبط `.env`

---

## صلاحيات المجلدات

تأكد من الصلاحيات التالية:

| المجلد | الصلاحيات |
|--------|-----------|
| storage | 775 |
| bootstrap/cache | 775 |

```bash
chmod -R 775 storage bootstrap/cache
```

---

## SSL (HTTPS)

- فعّل شهادة SSL للنطاق من لوحة التحكم
- تأكد أن `FORCE_HTTPS=true` و `APP_URL=https://elitevape.online` في `.env`

---

## استكشاف الأخطاء

| المشكلة | الحل |
|---------|------|
| 500 Internal Server Error | تحقق من صلاحيات المجلدات وملف `.env` |
| قاعدة البيانات لا تتصل | راجع `DB_HOST` (غالباً `localhost`) وبيانات الاتصال |
| الصور لا تظهر | نفّذ `php artisan storage:link` |
| صفحة بيضاء | تأكد من `APP_DEBUG=true` مؤقتاً لرؤية الخطأ، ثم أعدها إلى `false` |

---

## ملاحظات أمنية

- لا ترفع ملف `.env` إلى مستودع عام
- احتفظ بنسخة احتياطية من قاعدة البيانات بانتظام
- استخدم `APP_DEBUG=false` في الإنتاج
