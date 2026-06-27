# قائمة API Endpoints — api/v1

> **صيغة الاستجابة الموحدة (للمستقبل):** `{ success, data?, errors?, message?, meta? }`

| Method | Endpoint | Auth | الوصف |
|--------|----------|------|-------|
| POST | auth/registration | - | تسجيل عميل |
| POST | auth/login | - | تسجيل دخول |
| POST | auth/social-customer-login | - | تسجيل دخول اجتماعي |
| POST | auth/check-phone | - | التحقق من رقم الهاتف |
| POST | auth/verify-phone | - | التحقق من OTP |
| POST | auth/check-email | - | التحقق من البريد |
| POST | auth/verify-email | - | التحقق من البريد |
| POST | auth/firebase-auth-verify | - | التحقق Firebase |
| POST | auth/verify-otp | - | التحقق من OTP |
| POST | auth/registration-with-otp | - | تسجيل مع OTP |
| POST | auth/existing-account-check | - | فحص وجود حساب |
| POST | auth/registration-with-social-media | - | تسجيل عبر وسائل التواصل |
| POST | auth/forgot-password | - | نسيت كلمة المرور |
| POST | auth/verify-token | - | التحقق من رمز إعادة التعيين |
| PUT | auth/reset-password | - | إعادة تعيين كلمة المرور |
| POST | auth/delivery-man/register | - | تسجيل مندوب |
| POST | auth/delivery-man/login | - | دخول مندوب |
| GET | config/ | - | إعدادات التطبيق |
| GET | config/delivery-fee | - | رسوم التوصيل |
| GET | config/delivery-charge | - | رسوم حسب المنطقة |
| GET | products/latest | - | أحدث المنتجات |
| GET | products/discounted | - | المنتجات المخفضة |
| GET | products/search | - | بحث |
| GET | products/details/{id} | - | تفاصيل منتج |
| GET | products/related-products/{product_id} | - | منتجات ذات صلة |
| GET | products/reviews/{product_id} | - | تقييمات المنتج |
| GET | products/rating/{product_id} | - | تقييم المنتج |
| GET | products/new-arrival | - | وصول جديد |
| POST | products/reviews/submit | Bearer | إرسال تقييم |
| GET | banners/ | - | البانرات |
| GET | categories/ | - | التصنيفات |
| GET | categories/childes/{category_id} | - | التصنيفات الفرعية |
| GET | categories/products/{category_id} | - | منتجات التصنيف |
| GET | categories/products/{category_id}/all | - | كل المنتجات |
| GET | categories/featured | - | تصنيفات مميزة |
| GET | categories/popular | - | تصنيفات شائعة |
| GET | pages | - | الصفحات |
| GET | language/ | - | اللغات |
| GET | user-types | - | أنواع المستخدمين |
| POST | contact-us | - | تواصل معنا |
| GET | flash-sale | - | عرض فلاش |
| POST | guest/add | - | إضافة ضيف |
| POST | fcm-subscribe-to-topic | - | اشتراك FCM |
| GET | coupon/list | guest-id | قائمة الكوبونات |
| GET | coupon/apply | guest-id | تطبيق كوبون |
| GET | notifications/ | guest-id | الإشعارات |
| GET | customer/info | Bearer | معلومات العميل |
| PUT | customer/update-profile | Bearer | تحديث الملف |
| PUT | customer/cm-firebase-token | Bearer | تحديث FCM |
| POST | customer/verify-profile-info | Bearer | التحقق من الملف |
| DELETE | customer/remove-account | Bearer | حذف الحساب |
| GET | customer/address/list | Bearer+guest | عناوين |
| POST | customer/address/add | Bearer+guest | إضافة عنوان |
| PUT | customer/address/update/{id} | Bearer+guest | تحديث عنوان |
| DELETE | customer/address/delete | Bearer+guest | حذف عنوان |
| GET | customer/order/list | Bearer+guest | قائمة الطلبات |
| POST | customer/order/details | Bearer+guest | تفاصيل الطلب |
| POST | customer/order/place | Bearer+guest | إنشاء طلب |
| PUT | customer/order/cancel | Bearer+guest | إلغاء طلب |
| POST | customer/order/track | Bearer+guest | تتبع الطلب |
| PUT | customer/order/payment-method | Bearer+guest | تحديث طريقة الدفع |
| GET | customer/reorder/products | Bearer | منتجات إعادة الطلب |
| POST | customer/payment-mobile | Bearer+guest | دفع |
| GET | customer/wish-list/ | Bearer | المفضلة |
| POST | customer/wish-list/add | Bearer | إضافة للمفضلة |
| DELETE | customer/wish-list/remove | Bearer | حذف من المفضلة |
| GET | customer/loyalty | Bearer | رصيد نقاط الولاء |
| GET | customer/loyalty/history | Bearer | سجل النقاط |
| GET | delivery-man/profile | token | ملف المندوب |
| GET | delivery-man/current-orders | token | الطلبات الحالية |
| GET | delivery-man/all-orders | token | كل الطلبات |
| GET | delivery-man/orders-count | token | عدد الطلبات |
| POST | delivery-man/record-location-data | token | تسجيل الموقع |
| GET | delivery-man/order-delivery-history | token | سجل التوصيل |
| PUT | delivery-man/update-order-status | token | تحديث حالة الطلب |
| PUT | delivery-man/update-payment-status | token | تحديث حالة الدفع |
| GET | delivery-man/order-details | token | تفاصيل الطلب |
| GET | delivery-man/last-location | token | آخر موقع |
| PUT | delivery-man/update-fcm-token | token | تحديث FCM |
| GET | delivery-man/order-model | token | نموذج الطلب |
| DELETE | delivery-man/remove-account | token | حذف الحساب |
| GET | delivery-man/reviews/{delivery_man_id} | Bearer | تقييمات المندوب |
| GET | delivery-man/reviews/rating/{delivery_man_id} | Bearer | تقييم المندوب |
| POST | delivery-man/reviews/submit | Bearer | إرسال تقييم |

**Headers:** `Authorization: Bearer {token}`, `X-localization: ar|en|he`, `guest-id: {id}` (للضيف)
