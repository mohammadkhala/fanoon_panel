<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessPagesSeeder extends Seeder
{
    public function run(): void
    {
        $aboutUsAr = '<h3>من نحن</h3>
<p>مرحباً بكم في متجرنا الإلكتروني المتخصص في منتجات السجائر الإلكترونية والسوائل. نقدم لكم تشكيلة واسعة من المنتجات عالية الجودة بأسعار منافسة.</p>
<h4>قصتنا</h4>
<p>انطلقنا من رغبة في توفير بدائل آمنة وعالية الجودة لعشاق السجائر الإلكترونية. نتعامل مع موردين معتمدين ونختبر منتجاتنا بعناية لضمان رضاكم.</p>
<h4>رؤيتنا</h4>
<p>أن نكون الخيار الأول للعملاء في مجال منتجات الفيب والسجائر الإلكترونية، مع تقديم خدمة عملاء متميزة وضمان جودة المنتجات.</p>
<h4>قيمنا</h4>
<ul>
<li>الجودة والموثوقية</li>
<li>رضا العملاء</li>
<li>الشفافية في التعامل</li>
<li>الدعم المستمر قبل وبعد البيع</li>
</ul>
<h4>لماذا تختارنا؟</h4>
<ul>
<li>تشكيلة واسعة من الأجهزة والسوائل</li>
<li>أسعار منافسة وعروض دورية</li>
<li>توصيل سريع لجميع المناطق</li>
<li>ضمان على المنتجات</li>
</ul>
<p><strong>للاستفسارات:</strong> تواصل معنا عبر صفحة اتصل بنا أو عبر البريد الإلكتروني ورقم الهاتف الموجودين في الموقع.</p>';

        $aboutUsEn = '<h3>About Us</h3>
<p>Welcome to our online store specializing in e-cigarettes and e-liquids. We offer a wide range of high-quality products at competitive prices.</p>
<h4>Our Story</h4>
<p>We started with a desire to provide safe, high-quality alternatives for e-cigarette enthusiasts. We work with certified suppliers and carefully test our products to ensure your satisfaction.</p>
<h4>Our Vision</h4>
<p>To be the first choice for customers in the vape and e-cigarette products sector, with excellent customer service and product quality guarantee.</p>
<h4>Our Values</h4>
<ul>
<li>Quality and Reliability</li>
<li>Customer Satisfaction</li>
<li>Transparency in Dealing</li>
<li>Ongoing support before and after sale</li>
</ul>
<h4>Why Choose Us?</h4>
<ul>
<li>Wide range of devices and e-liquids</li>
<li>Competitive prices and regular offers</li>
<li>Fast delivery to all areas</li>
<li>Product warranty</li>
</ul>
<p><strong>For inquiries:</strong> Contact us through our contact page or via the email and phone number listed on the website.</p>';

        $this->seedPage('about_us', json_encode(['ar' => $aboutUsAr, 'en' => $aboutUsEn]));

        $privacyAr = '<h3>سياسة الخصوصية</h3>
<p>نحن نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية. توضح هذه السياسة كيفية جمع واستخدام وحماية معلوماتك عند استخدام موقعنا.</p>
<h4>المعلومات التي نجمعها</h4>
<ul>
<li>الاسم الكامل والبريد الإلكتروني ورقم الهاتف</li>
<li>عنوان التوصيل والموقع الجغرافي</li>
<li>سجل الطلبات والمعاملات والدفعات</li>
<li>بيانات التصفح (عنوان IP، نوع المتصفح، الصفحات المزارة)</li>
<li>تفضيلات التسوق والإشعارات</li>
</ul>
<h4>استخدام المعلومات</h4>
<p>نستخدم بياناتك لمعالجة الطلبات، وتحسين خدماتنا، وإرسال عروض قد تهمك (يمكنك إلغاء الاشتراك في أي وقت)، وتحليل سلوك المستخدمين لتحسين تجربة التسوق.</p>
<h4>ملفات تعريف الارتباط (Cookies)</h4>
<p>نستخدم ملفات تعريف الارتباط لتحسين تجربتك في الموقع وتذكر تفضيلاتك. يمكنك تعطيلها من إعدادات المتصفح.</p>
<h4>مشاركة البيانات</h4>
<p>لا نبيع بياناتك لأطراف ثالثة. قد نشارك معلومات محدودة مع شركات الشحن والدفع فقط لتنفيذ طلباتك.</p>
<h4>حقوقك</h4>
<p>لديك الحق في طلب نسخة من بياناتك أو تصحيحها أو حذفها. تواصل معنا عبر صفحة اتصل بنا.</p>
<h4>حماية البيانات</h4>
<p>نطبق إجراءات أمنية مناسبة (تشفير، جدران حماية) لحماية بياناتك من الوصول غير المصرح به أو التسريب.</p>
<p><em>آخر تحديث: مارس 2026</em></p>';

        $privacyEn = '<h3>Privacy Policy</h3>
<p>We respect your privacy and are committed to protecting your personal data. This policy explains how we collect, use, and protect your information when using our website.</p>
<h4>Information We Collect</h4>
<ul>
<li>Full name, email, and phone number</li>
<li>Delivery address and location</li>
<li>Order, transaction, and payment history</li>
<li>Browsing data (IP address, browser type, pages visited)</li>
<li>Shopping preferences and notifications</li>
</ul>
<h4>Use of Information</h4>
<p>We use your data to process orders, improve our services, send offers that may interest you (you can unsubscribe at any time), and analyze user behavior to enhance the shopping experience.</p>
<h4>Cookies</h4>
<p>We use cookies to improve your site experience and remember your preferences. You can disable them from your browser settings.</p>
<h4>Data Sharing</h4>
<p>We do not sell your data to third parties. We may share limited information with shipping and payment companies only to fulfill your orders.</p>
<h4>Your Rights</h4>
<p>You have the right to request a copy of your data, correct it, or delete it. Contact us through our contact page.</p>
<h4>Data Protection</h4>
<p>We apply appropriate security measures (encryption, firewalls) to protect your data from unauthorized access or leakage.</p>
<p><em>Last updated: March 2026</em></p>';

        $this->seedPage('privacy_policy', json_encode(['ar' => $privacyAr, 'en' => $privacyEn]));

        $tncAr = '<h3>الشروط والأحكام</h3>
<p>باستخدام هذا الموقع، فإنك توافق على الالتزام بهذه الشروط والأحكام. ننصح بقراءتها قبل إتمام أي عملية شراء.</p>
<h4>الأهلية والاستخدام</h4>
<p>يجب أن تكون بالغاً (18 عاماً فأكثر) لاستخدام منتجاتنا. نحتفظ بحق رفض الطلبات التي لا تستوفي الشروط.</p>
<h4>الطلب والدفع</h4>
<ul>
<li>الأسعار المعروضة شاملة الضريبة ما لم يُذكر خلاف ذلك</li>
<li>قد يكون هناك حد أدنى للطلب حسب المنطقة</li>
<li>نقبل الدفع عند الاستلام والتحويل البنكي وبطاقات الائتمان</li>
<li>الطلب يُعتبر ملزماً بعد تأكيدنا له عبر البريد أو الهاتف</li>
</ul>
<h4>التوصيل</h4>
<p>نقوم بالتوصيل خلال 2-5 أيام عمل حسب المنطقة. قد تطول المدة في المناسبات الخاصة. رسوم التوصيل تُحسب عند إتمام الطلب.</p>
<h4>المنتجات</h4>
<p>نضمن أن المنتجات مطابقة للوصف. في حال وجود خلل، يرجى التواصل خلال 48 ساعة من الاستلام. الصور توضيحية وقد تختلف قليلاً عن المنتج الفعلي.</p>
<h4>الملكية الفكرية</h4>
<p>جميع العلامات التجارية والشعارات والمحتوى في الموقع مملوكة لنا أو لشركائنا. يُمنع النسخ أو الاستخدام دون إذن.</p>
<p>لأي استفسار: راجع سياسات الإرجاع والاسترداد والإلغاء.</p>';

        $tncEn = '<h3>Terms and Conditions</h3>
<p>By using this website, you agree to comply with these terms and conditions. We recommend reading them before completing any purchase.</p>
<h4>Eligibility and Use</h4>
<p>You must be of legal age (18 years or older) to use our products. We reserve the right to refuse orders that do not meet the conditions.</p>
<h4>Orders and Payment</h4>
<ul>
<li>Prices displayed include tax unless otherwise stated</li>
<li>There may be a minimum order amount depending on the area</li>
<li>We accept cash on delivery, bank transfer, and credit cards</li>
<li>The order is binding after our confirmation via email or phone</li>
</ul>
<h4>Delivery</h4>
<p>We deliver within 2-5 business days depending on the area. Delivery may take longer during special occasions. Shipping fees are calculated when completing the order.</p>
<h4>Products</h4>
<p>We guarantee that products match the description. In case of defect, please contact us within 48 hours of receipt. Images are illustrative and may differ slightly from the actual product.</p>
<h4>Intellectual Property</h4>
<p>All trademarks, logos, and content on the site are owned by us or our partners. Copying or use without permission is prohibited.</p>
<p>For any inquiry: See our return, refund, and cancellation policies.</p>';

        $this->seedPage('terms_and_conditions', json_encode(['ar' => $tncAr, 'en' => $tncEn]));

        $cancellationAr = '<h3>سياسة الإلغاء</h3>
<p>يمكنك إلغاء طلبك في الحالات التالية. نحرص على تسهيل عملية الإلغاء قدر الإمكان.</p>
<h4>قبل الشحن</h4>
<p>إذا لم يتم شحن طلبك بعد، يمكنك طلب الإلغاء عبر التواصل معنا (واتساب، هاتف، بريد إلكتروني). سنقوم بإلغاء الطلب واسترداد المبلغ خلال 5-7 أيام عمل.</p>
<h4>بعد الشحن</h4>
<p>بعد شحن الطلب، يخضع الإلغاء لسياسة الإرجاع. يرجى مراجعة سياسة الإرجاع للتفاصيل. قد تُخصم رسوم الشحن في بعض الحالات.</p>
<h4>طريقة طلب الإلغاء</h4>
<ol>
<li>تواصل مع خدمة العملاء مع رقم الطلب</li>
<li>أوضح سبب الإلغاء (اختياري)</li>
<li>انتظر تأكيد الإلغاء خلال 24 ساعة</li>
</ol>
<h4>ملاحظات</h4>
<ul>
<li>الطلبات المدفوعة عند الاستلام يمكن إلغاؤها دون رسوم إضافية</li>
<li>الطلبات المدفوعة مسبقاً يتم استردادها وفق سياسة الاسترداد</li>
<li>الطلبات المجانية أو ذات العروض الخاصة قد تخضع لشروط إضافية</li>
</ul>';

        $cancellationEn = '<h3>Cancellation Policy</h3>
<p>You can cancel your order in the following cases. We strive to make the cancellation process as easy as possible.</p>
<h4>Before Shipping</h4>
<p>If your order has not been shipped yet, you can request cancellation by contacting us (WhatsApp, phone, email). We will cancel the order and refund the amount within 5-7 business days.</p>
<h4>After Shipping</h4>
<p>After the order is shipped, cancellation is subject to the return policy. Please refer to the return policy for details. Shipping fees may be deducted in some cases.</p>
<h4>How to Request Cancellation</h4>
<ol>
<li>Contact customer service with your order number</li>
<li>State the reason for cancellation (optional)</li>
<li>Wait for cancellation confirmation within 24 hours</li>
</ol>
<h4>Notes</h4>
<ul>
<li>Cash on delivery orders can be cancelled without additional fees</li>
<li>Prepaid orders are refunded according to the refund policy</li>
<li>Free or special offer orders may be subject to additional terms</li>
</ul>';

        $this->seedPageWithStatus('cancellation_page', ['ar' => $cancellationAr, 'en' => $cancellationEn], 1);

        $refundAr = '<h3>سياسة الاسترداد</h3>
<p>نلتزم برضاكم. في حال عدم رضاك عن المنتج، يمكنك طلب الاسترداد وفق الشروط التالية:</p>
<h4>شروط الاسترداد</h4>
<ul>
<li>المنتج غير مستخدم وفي عبواته الأصلية</li>
<li>التواصل خلال 7 أيام من الاستلام</li>
<li>وجود فاتورة أو إثبات شراء</li>
<li>المنتج غير ضمن قائمة المنتجات المستثناة (انظر أدناه)</li>
</ul>
<h4>إجراءات الاسترداد</h4>
<ol>
<li>تواصل مع خدمة العملاء مع رقم الطلب</li>
<li>سنراجع طلبك خلال 24-48 ساعة</li>
<li>بعد الموافقة، يتم استرداد المبلغ خلال 5-7 أيام عمل</li>
<li>في حال الدفع عند الاستلام، قد نطلب تفاصيل بنكية للتحويل</li>
</ol>
<h4>طرق الاسترداد</h4>
<p>يتم الاسترداد بنفس طريقة الدفع الأصلية (تحويل بنكي أو رصيد في المحفظة). للدفع عند الاستلام، نستخدم التحويل البنكي.</p>
<h4>منتجات مستثناة من الاسترداد</h4>
<p>السوائل المفتوحة، المنتجات الاستهلاكية المستخدمة، والعروض الترويجية المحددة قد لا تكون قابلة للاسترداد. تواصل معنا للاستفسار.</p>';

        $refundEn = '<h3>Refund Policy</h3>
<p>We are committed to your satisfaction. If you are not satisfied with the product, you can request a refund according to the following conditions:</p>
<h4>Refund Conditions</h4>
<ul>
<li>Product is unused and in original packaging</li>
<li>Contact within 7 days of receipt</li>
<li>Invoice or proof of purchase available</li>
<li>Product is not in the excluded items list (see below)</li>
</ul>
<h4>Refund Procedures</h4>
<ol>
<li>Contact customer service with order number</li>
<li>We will review your request within 24-48 hours</li>
<li>After approval, refund is processed within 5-7 business days</li>
<li>For cash on delivery, we may request bank details for transfer</li>
</ol>
<h4>Refund Methods</h4>
<p>Refund is made using the original payment method (bank transfer or wallet balance). For cash on delivery, we use bank transfer.</p>
<h4>Excluded from Refund</h4>
<p>Opened e-liquids, used consumables, and certain promotional offers may not be refundable. Contact us for inquiries.</p>';

        $this->seedPageWithStatus('refund_page', ['ar' => $refundAr, 'en' => $refundEn], 1);

        $returnAr = '<h3>سياسة الإرجاع</h3>
<p>نسمح بإرجاع المنتجات في الحالات التالية. نهدف إلى حل أي مشكلة بسرعة وشفافية.</p>
<h4>أسباب الإرجاع المقبولة</h4>
<ul>
<li>منتج معيب أو مختلف عن الوصف</li>
<li>تلف أثناء الشحن (مع إرفاق صور)</li>
<li>خطأ في الشحن (منتج خاطئ أو ناقص)</li>
<li>منتج منتهي الصلاحية</li>
</ul>
<h4>خطوات الإرجاع</h4>
<ol>
<li>تواصل معنا خلال 48 ساعة من الاستلام</li>
<li>احتفظ بالمنتج في حالته الأصلية مع العبوة والفاتورة</li>
<li>سنحدد لك طريقة إرجاع مناسبة (استلام من المنزل أو نقطة تجميع)</li>
<li>بعد استلام المنتج والتحقق منه، نستبدله أو نسترد المبلغ خلال 5-7 أيام</li>
</ol>
<h4>تكلفة الشحن</h4>
<p>في حال الخطأ من جهتنا، نتحمل تكلفة الإرجاع. في حال تغيير الرأي، قد يتحمل العميل تكلفة الإرجاع.</p>
<h4>منتجات غير قابلة للإرجاع</h4>
<p>السوائل المفتوحة والمنتجات الاستهلاكية المستخدمة لا يمكن إرجاعها لأسباب صحية.</p>
<h4>مدة المعالجة</h4>
<p>معالجة طلبات الإرجاع تستغرق 5-10 أيام عمل من استلام المنتج.</p>';

        $returnEn = '<h3>Return Policy</h3>
<p>We allow product returns in the following cases. We aim to resolve any issue quickly and transparently.</p>
<h4>Acceptable Return Reasons</h4>
<ul>
<li>Defective product or different from description</li>
<li>Damage during shipping (with photos attached)</li>
<li>Shipping error (wrong or missing product)</li>
<li>Expired product</li>
</ul>
<h4>Return Steps</h4>
<ol>
<li>Contact us within 48 hours of receipt</li>
<li>Keep the product in its original condition with packaging and invoice</li>
<li>We will determine a suitable return method (home pickup or collection point)</li>
<li>After receiving and verifying the product, we will replace or refund within 5-7 days</li>
</ol>
<h4>Shipping Costs</h4>
<p>If the error is on our side, we bear the return shipping cost. In case of change of mind, the customer may bear the return cost.</p>
<h4>Non-Returnable Products</h4>
<p>Opened e-liquids and used consumables cannot be returned for health reasons.</p>
<h4>Processing Time</h4>
<p>Return request processing takes 5-10 business days from product receipt.</p>';

        $this->seedPageWithStatus('return_page', ['ar' => $returnAr, 'en' => $returnEn], 1);

        $this->command->info('تم إضافة المحتوى الافتراضي لصفحات الأعمال (من نحن، الخصوصية، الشروط، الإلغاء، الاسترداد، الإرجاع).');
    }

    private function seedPage(string $key, string $value): void
    {
        $exists = DB::table('business_settings')->where('key', $key)->first();
        $now = now();
        if ($exists) {
            DB::table('business_settings')->where('key', $key)->update(['value' => $value, 'updated_at' => $now]);
        } else {
            DB::table('business_settings')->insert(['key' => $key, 'value' => $value, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    private function seedPageWithStatus(string $key, array $content, int $status = 1): void
    {
        $value = json_encode(['status' => $status, 'content' => $content]);
        $exists = DB::table('business_settings')->where('key', $key)->first();
        $now = now();
        if ($exists) {
            DB::table('business_settings')->where('key', $key)->update(['value' => $value, 'updated_at' => $now]);
        } else {
            DB::table('business_settings')->insert(['key' => $key, 'value' => $value, 'created_at' => $now, 'updated_at' => $now]);
        }
    }
}
