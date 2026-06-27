# خطة علاجية — المراحل أ، ب، ج

> **التاريخ:** 2025-03
> **المرجع:** PLAN-REMAINING-TASKS.md

---

## المرحلة أ — مكتملة ✅

| # | المهمة | الحالة | الاختبار |
|---|--------|--------|----------|
| 1 | تصدير قائمة العملاء إلى Excel | ✅ | `PhaseATest::test_customer_export_*` |
| 2 | فلتر التقييم للمنتجات | ✅ | `PhaseATest::test_product_list_rating_filter` |
| 3 | شارات مراجعة للعملاء | ✅ | `PhaseATest::test_customer_badge_logic_*` |

**تشغيل الاختبارات:**
```bash
php artisan test tests/Feature/PhaseATest.php
```

---

## المرحلة ب — مكتملة ✅

| # | المهمة | الحالة | الاختبار |
|---|--------|--------|----------|
| 1 | تقسيم العملاء (VIP، متكرر، حديث، غير نشط) | ✅ | `PhaseBTest::test_customer_list_segment_filter` |
| 2 | طباعة جماعية للفواتير | ✅ | `PhaseBTest::test_order_list_page_has_bulk_print_elements` |
| 3 | بحث موحّد في لوحة التحكم | ✅ | `PhaseBTest::test_unified_search_*` |

**تشغيل الاختبارات:**
```bash
php artisan test tests/Feature/PhaseBTest.php
```

---

## المرحلة ج — اختيارية

| # | المهمة | الملاحظة |
|---|--------|----------|
| 1 | بحث بالخصائص (attributes) | عند الحاجة |

---

## ملخص التنفيذ

- **المرحلة أ:** تم تنفيذها واختبارها ✅
- **المرحلة ب:** جاهزة للبدء
- **المرحلة ج:** مؤجلة
