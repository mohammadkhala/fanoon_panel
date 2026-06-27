# دليل Redis والكاش للإنتاج

> **الهدف:** تحسين الأداء في بيئة الإنتاج باستخدام Redis للكاش والجلسات.

---

## 1. متى تستخدم Redis؟

- **الكاش:** تخزين مؤقت للبيانات المتكررة (التصنيفات، إعدادات المتجر، أعداد الطلبات)
- **الجلسات:** جلسات أسرع وقابلة للمشاركة بين عدة خوادم
- **الصفوف (Queue):** معالجة المهام في الخلفية (إرسال بريد، إشعارات)

---

## 2. التثبيت

### على Ubuntu/Debian
```bash
sudo apt update
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

### التحقق
```bash
redis-cli ping
# يجب أن يرجع: PONG
```

---

## 3. إعداد Laravel

### 3.1 ملف `.env` (الإنتاج)
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3.2 إنشاء كاش مخصص (اختياري)
في `config/cache.php` يمكنك إضافة store مخصص:
```php
'orders_sidebar' => [
    'driver' => 'redis',
    'connection' => 'cache',
    'lock_connection' => 'default',
],
```

---

## 4. مسح الكاش

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 5. ملاحظات

- إذا لم يكن Redis متاحاً، Laravel يعود تلقائياً لـ `file` حسب الإعداد الافتراضي
- لا تستخدم Redis في التطوير المحلي إلا إذا كنت تختبر سلوك الكاش
- في الإنتاج، تأكد من أن Redis محمي (كلمة مرور، عدم التعرض للإنترنت مباشرة)
