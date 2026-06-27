<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WaSender WhatsApp API Service
 * Docs: https://wasenderapi.com/api-docs/messages/send-text-message
 */
class WaSenderService
{
    private string $apiKey;
    private string $sessionId;
    private string $baseUrl = 'https://www.wasenderapi.com/api';

    public function __construct()
    {
        $this->apiKey    = config('wasender.api_key', '');
        $this->sessionId = config('wasender.session', '');
    }

    /**
     * Send a plain text WhatsApp message.
     *
     * @param string $phone  Customer phone (local 05x or E.164 +970...)
     * @param string $text   Message body (Arabic or English)
     * @return bool
     */
    public function sendMessage(string $phone, string $text): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('WaSender: api_key is not configured.');
            return false;
        }

        $to = $this->normalizePhone($phone);
        if (empty($to)) {
            Log::warning('WaSender: invalid phone number: ' . $phone);
            return false;
        }

        try {
            $payload = ['to' => $to, 'text' => $text];
            if (!empty($this->sessionId)) {
                $payload['session_id'] = $this->sessionId;
            }

            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post($this->baseUrl . '/send-message', $payload);

            if ($response->successful()) {
                Log::info("WaSender: sent to {$to}");
                return true;
            }

            Log::warning('WaSender: failed (' . $response->status() . ') ' . $response->body());
            return false;

        } catch (\Throwable $e) {
            Log::error('WaSender exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalize phone to E.164 format.
     * Handles all common input formats for Palestinian numbers:
     *
     *  +972594513978   → +972594513978  (already E.164 with +972)
     *  00972594513978  → +972594513978  (international with 00 prefix)
     *   972594513978   → +972594513978  (no + prefix)
     *    0594513978    → +972594513978  (local with leading 0)
     *     594513978    → +972594513978  (bare 9-digit, assumed Palestinian)
     *  +970594513978   → +972594513978  (old +970 code → converted to +972)
     *  00970594513978  → +972594513978  (old 00970 prefix → converted to +972)
     *   970594513978   → +972594513978  (old 970 prefix → converted to +972)
     *
     * Note: Palestinian WhatsApp numbers are registered under +972 (Israeli network),
     *       not +970 (Palestinian Authority code).
     */
    private function normalizePhone(string $phone): string
    {
        // 1. اسحب كل شيء ماعدا الأرقام والـ +
        $digits = preg_replace('/[^\d+]/', '', trim($phone));

        if (empty($digits)) {
            return '';
        }

        // 2. أزل الـ + الأول إن وجد وأعمل على الأرقام فقط
        $stripped = ltrim($digits, '+');

        // 3. أزل مقدمة 00 الدولية
        if (str_starts_with($stripped, '00')) {
            $stripped = substr($stripped, 2);
        }

        // 4. الآن $stripped يحتوي الأرقام فقط بدون مقدمة دولية

        // حالة: يبدأ بـ 972 (مقدمة فلسطين/إسرائيل الصحيحة على واتساب)
        if (str_starts_with($stripped, '972')) {
            return '+' . $stripped;             // +972XXXXXXXXX
        }

        // حالة: يبدأ بـ 970 (المقدمة الرسمية للسلطة الفلسطينية — نحولها لـ 972)
        if (str_starts_with($stripped, '970')) {
            return '+972' . substr($stripped, 3); // نستبدل 970 بـ 972
        }

        // حالة: رقم محلي يبدأ بـ 0 (0594513978 → 9 أرقام بعد حذف الصفر)
        if (str_starts_with($stripped, '0') && strlen($stripped) >= 9) {
            return '+972' . substr($stripped, 1); // حذف الـ 0 وإضافة +972
        }

        // حالة: أرقام بدون مقدمة (594513978 — 9 أرقام فلسطينية)
        if (strlen($stripped) === 9 && str_starts_with($stripped, '5')) {
            return '+972' . $stripped;
        }

        // حالة عامة: أضف + فقط
        if (!empty($stripped)) {
            return '+' . $stripped;
        }

        return '';
    }

    // ──────────────────────────────────────────────
    //  Predefined Arabic message templates
    // ──────────────────────────────────────────────

    /**
     * Build an Arabic order-status message for the customer.
     */
    public static function buildOrderStatusMessage(
        int    $orderId,
        string $newStatus,
        string $storeName = 'المتجر',
        ?string $note = null
    ): string {
        $statusLabels = [
            'pending'          => '⏳ قيد الانتظار',
            'confirmed'        => '✅ مؤكّد',
            'processing'       => '⚙️ قيد التجهيز',
            'out_for_delivery' => '🚚 خرج للتوصيل',
            'delivered'        => '🎉 تم التوصيل',
            'returned'         => '↩️ مُرجَع',
            'failed'           => '❌ فشل التوصيل',
            'canceled'         => '🚫 مُلغى',
        ];

        $label = $statusLabels[$newStatus] ?? "📦 {$newStatus}";

        $msg  = "🏪 *{$storeName}*\n";
        $msg .= "──────────────────\n";
        $msg .= "مرحباً! تم تحديث حالة طلبك.\n\n";
        $msg .= "🧾 رقم الطلب: *#{$orderId}*\n";
        $msg .= "📌 الحالة الجديدة: {$label}\n";

        if (!empty($note)) {
            $msg .= "📝 ملاحظة: {$note}\n";
        }

        $msg .= "──────────────────\n";
        $msg .= "شكراً لثقتك بنا 🙏";

        return $msg;
    }
}
