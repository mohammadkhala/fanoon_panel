<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure FCM customer messages have default marketing values.
     * Updates empty messages only. Does not overwrite user customizations.
     * Deliveryman messages (removed from UI) are not modified.
     */
    public function up(): void
    {
        $defaults = [
            'order_pending_message' => 'شكراً لثقتك! استلمنا طلبك ونجهّزه خصيصاً لك — سنؤكد لك فور الجاهزية',
            'order_confirmation_msg' => 'خبر سار! طلبك مؤكد ونحضّره بعناية. سنصل إليك في الموعد — ننتظر رأيك',
            'order_processing_message' => 'طلبك بين أيدينا الآن ونعطيه كل الاهتمام. سنخبرك عند خروجه للتوصيل',
            'out_for_delivery_message' => 'طلبك في الطريق إليك! شكراً لصبرك — سنصل قريباً ونتمنى تجربة رائعة',
            'order_delivered_message' => 'تم التوصيل بنجاح! شكراً لاختيارك لنا. رأيك يهمنا — شاركنا تجربتك',
            'returned_message' => 'شكراً لتواصلك. استلمنا طلب الإرجاع وفريقنا سيتواصل معك خلال 24 ساعة',
            'failed_message' => 'نعتذر عن الإزعاج. واجهنا صعوبة — سنتواصل معك فوراً لترتيب أفضل حل. ثقتك تهمنا',
            'canceled_message' => 'تم تنفيذ طلب الإلغاء. نأمل خدمتك مجدداً — نحن هنا لخدمتك عند حاجتك',
        ];

        $now = now();

        foreach ($defaults as $key => $defaultMessage) {
            $row = DB::table('business_settings')->where('key', $key)->first();

            if ($row) {
                $decoded = json_decode($row->value, true);
                $currentMessage = $decoded['message'] ?? '';
                $currentStatus = $decoded['status'] ?? 0;

                if (trim($currentMessage) === '') {
                    DB::table('business_settings')
                        ->where('key', $key)
                        ->update([
                            'value' => json_encode([
                                'status' => $currentStatus,
                                'message' => $defaultMessage,
                            ]),
                            'updated_at' => $now,
                        ]);
                }
            } else {
                DB::table('business_settings')->insert([
                    'key' => $key,
                    'value' => json_encode([
                        'status' => 0,
                        'message' => $defaultMessage,
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        Cache::forget('cache_business_settings_table');
    }

    public function down(): void
    {
        // لا نرجع — قد يكون المستخدم عدّل القيم
    }
};
