# دليل المبرمج — Elite Vape Backend

> **للمبرمج الجديد:** هذا الملف يشرح كل ما تم بناؤه في المشروع، أين توجد الملفات، وكيفية العمل عليها.

---

## 1. ما هو هذا المشروع؟

**Elite Vape** منصة إدارة متجر إلكتروني تتكون من:

| المكون | الوصف |
|--------|-------|
| **لوحة تحكم (Admin)** | إدارة المنتجات، الطلبات، العملاء، التقارير، الإعدادات |
| **API للتطبيق** | تطبيق الموبايل يتصل بهذا الـ API |
| **واجهة الفرع** | واجهة محدودة للفروع (مخفية افتراضياً) |

---

## 2. التقنيات المستخدمة

| التقنية | الإصدار |
|---------|---------|
| **Laravel** | 12 |
| **PHP** | 8.2+ |
| **MySQL** | 8+ |
| **Laravel Passport** | مصادقة OAuth2 للـ API |
| **Blade** | قوالب لوحة التحكم |
| **Bootstrap 4** | واجهة لوحة التحكم |

---

## 3. بنية المشروع — أين أجد ماذا؟

```
elitevapeDB/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/              ← لوحة التحكم
│   │   │   ├── OrderController.php
│   │   │   ├── ProductController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── ReportController.php
│   │   │   ├── TagController.php
│   │   │   └── WebhookController.php
│   │   ├── Api/V1/             ← API للتطبيق
│   │   │   ├── OrderController.php
│   │   │   ├── ProductController.php
│   │   │   └── ...
│   │   └── Branch/             ← واجهة الفرع
│   ├── Models/
│   │   ├── Order.php
│   │   ├── Product.php
│   │   ├── Customer.php
│   │   ├── Tag.php
│   │   └── WebhookEndpoint.php
│   ├── Services/
│   │   ├── WebhookService.php
│   │   └── OrderStatusLogService.php
│   └── Jobs/
│       └── DispatchWebhookJob.php
├── database/
│   ├── migrations/             ← 2026_01_01 = Schema كامل
│   └── seeders/
├── resources/views/
│   └── admin-views/            ← صفحات لوحة التحكم
│       ├── product/
│       ├── order/
│       ├── customer/
│       ├── report/
│       ├── tag/
│       └── webhook/
├── routes/
│   ├── admin.php               ← مسارات لوحة التحكم
│   └── api/v1/api.php          ← مسارات API
├── config/
│   └── feature_flags.php       ← إخفاء/إظهار ميزات
└── docs/                       ← التوثيق
```

---

## 4. ما تم إنجازه — ملخص للمبرمج

### 4.1 المراحل أ، ب، ج (مكتملة)

| الميزة | الملفات | الوصف |
|--------|---------|-------|
| تصدير العملاء Excel | `CustomerController`, `exportCustomers` | تصدير قائمة العملاء |
| فلتر التقييم للمنتجات | `ProductController`, `product/list.blade.php` | فلتر حسب متوسط التقييم |
| شارات مراجعة | `Review` model, views | مشتري مؤكد، عميل موثوق |
| تقسيم العملاء | `CustomerController`, views | VIP، متكرر، حديث، غير نشط |
| بحث موحّد | `_sidebar.blade.php`, search modal | بحث في لوحة التحكم |
| بحث بالخصائص | `ProductController`, `product/list.blade.php` | فلتر حسب attributes |

### 4.2 المرحلة د — تحسينات أوسع

| المهمة | الحالة | الملفات |
|--------|--------|---------|
| **د1** CORS، Redis، حد API، lazy loading | ✅ | `config/cors.php`, `DEPLOYMENT-REDIS-CACHE.md` |
| **د2** توثيق OpenAPI | ✅ | `public/api-docs/openapi.yaml`, `/api-docs/` |
| **د3** Webhooks | ✅ | `WebhookEndpoint`, `WebhookService`, `DispatchWebhookJob` |
| **د4** تقارير متقدمة | ✅ جزئياً | `ReportController`, `best-selling-products`, `top-customers` |

### 4.3 وسوم المنتجات

| الميزة | الملفات |
|--------|---------|
| ترجمة الوسوم | `Tag.php` (translations), `TagController.php` |
| تبويبات لغات | `tag/list.blade.php` |
| ترجمة تلقائية | زر في النموذج → `admin.product.translate` |

---

## 5. الملفات المهمة — شرح سريع

### 5.1 Controllers

| Controller | المسؤولية |
|------------|-----------|
| `Admin/OrderController` | إدارة الطلبات، تغيير الحالة، سجل التغييرات |
| `Admin/ProductController` | المنتجات، الترجمة، التصدير |
| `Admin/CustomerController` | العملاء، التقسيم، التصدير |
| `Admin/ReportController` | التقارير، أفضل المنتجات، أفضل العملاء، Excel |
| `Admin/TagController` | الوسوم، الترجمة |
| `Admin/WebhookController` | إدارة Webhooks (مخفية افتراضياً) |
| `Api/V1/OrderController` | API الطلبات للتطبيق |

### 5.2 Models

| Model | ملاحظات |
|-------|---------|
| `Order` | علاقات: customer, branch, orderDetails |
| `Product` | ترجمات، خصائص، وسوم |
| `Customer` | نقاط الولاء، عناوين |
| `Tag` | ترجمات عبر `translations()` |
| `WebhookEndpoint` | url, events (JSON), secret |

### 5.3 Services

| Service | الوظيفة |
|---------|---------|
| `WebhookService` | إرسال أحداث `order.created`, `order.status_changed` |
| `OrderStatusLogService` | تسجيل تغييرات حالة الطلب |

### 5.4 Jobs

| Job | الوظيفة |
|-----|---------|
| `DispatchWebhookJob` | إرسال POST للـ webhook URL مع توقيع HMAC-SHA256 |

---

## 6. إعدادات Feature Flags

في `config/feature_flags.php`:

| المفتاح | الافتراضي | الوصف |
|---------|-----------|-------|
| `hide_webhooks` | true | إخفاء Webhooks من القائمة |
| `single_branch_mode` | true | فرع واحد فقط |
| `hide_branch_management` | true | إخفاء إدارة الفروع |

في `.env`:
- `HIDE_WEBHOOKS=false` → إظهار Webhooks
- `SINGLE_BRANCH_MODE=false` → دعم فروع متعددة

---

## 7. مسارات لوحة التحكم

| الصفحة | المسار |
|--------|--------|
| تسجيل الدخول | `/admin/auth/login` |
| لوحة التحكم الرئيسية | `/admin` |
| المنتجات | `/admin/product/list` |
| الطلبات | `/admin/order/list` |
| العملاء | `/admin/customer/list` |
| وسوم المنتجات | `/admin/tag/list` |
| أفضل المنتجات مبيعاً | `/admin/report/best-selling-products` |
| أفضل العملاء | `/admin/report/top-customers` |
| Webhooks | `/admin/webhook/list` (مخفية) |

---

## 8. API — للمبرمج التطبيق

- **توثيق:** `/api-docs/` (Swagger UI)
- **ملف OpenAPI:** `public/api-docs/openapi.yaml`
- **دليل مفصل:** [API-DEVELOPER-GUIDE.md](API-DEVELOPER-GUIDE.md)
- **قائمة Endpoints:** [API-ENDPOINTS-LIST.md](API-ENDPOINTS-LIST.md)

---

## 9. قاعدة البيانات

- **Migrations:** `database/migrations/`
- **Schema كامل:** migration بتاريخ `2026_01_01`
- **دليل النسخ الاحتياطي:** [DATABASE-BACKUP-GUIDE.md](DATABASE-BACKUP-GUIDE.md)

---

## 10. Webhooks — كيف تعمل؟

1. **جدول:** `webhook_endpoints` (name, url, events, secret, is_active)
2. **أحداث:** `order.created`, `order.status_changed`
3. **الإرسال:** Job `DispatchWebhookJob` يرسل POST مع:
   - `X-Webhook-Event`
   - `X-Webhook-Signature` (HMAC-SHA256)
4. **التفعيل:** `HIDE_WEBHOOKS=false` في `.env`
5. **Queue:** `php artisan queue:work` مطلوب للإرسال الفعلي

---

## 11. أوامر مفيدة

```bash
# تشغيل المشروع
php artisan serve

# مسح الكاش
php artisan view:clear && php artisan config:clear

# Queue (للـ Webhooks)
php artisan queue:work

# Migrations
php artisan migrate

# Passport
php artisan passport:keys
```

---

## 12. ما لم يُنجز بعد

| المهمة | الأولوية |
|--------|----------|
| رسوم بيانية (Chart.js) للتقارير | متوسطة |
| فلتر الفرع في التقارير | منخفضة |
| سجل إرسال Webhooks | منخفضة |

---

## 13. أين أجد المزيد؟

| الموضوع | الملف |
|---------|-------|
| آخر جلسة عمل | [SESSION-REPORT-CURRENT.md](SESSION-REPORT-CURRENT.md) |
| خطة المرحلة د | [PLAN-TREATMENT-PHASE-D.md](PLAN-TREATMENT-PHASE-D.md) |
| فهرس التوثيق | [README.md](README.md) |
| API | [API-DEVELOPER-GUIDE.md](API-DEVELOPER-GUIDE.md) |

---

*آخر تحديث: 2025-03*
