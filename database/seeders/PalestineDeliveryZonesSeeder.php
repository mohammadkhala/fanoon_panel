<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * مناطق التوصيل في فلسطين مع أسعارها
 * يُفعّل نوع التوصيل area-wise للفرع الافتراضي
 */
class PalestineDeliveryZonesSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = 1;

        // ──────────────────────────────────────────
        // 1. فعّل نوع التوصيل حسب المنطقة
        // ──────────────────────────────────────────
        DB::table('delivery_charge_setups')->updateOrInsert(
            ['branch_id' => $branchId],
            [
                'branch_id'                          => $branchId,
                'delivery_charge_type'               => 'area',
                'fixed_delivery_charge'              => 15,
                'delivery_charge_per_kilometer'      => 0,
                'minimum_delivery_charge'            => 5,
                'minimum_distance_for_free_delivery' => 0,
                'created_at'                         => now(),
                'updated_at'                         => now(),
            ]
        );

        // ──────────────────────────────────────────
        // 2. امسح المناطق القديمة (إن وُجدت)
        // ──────────────────────────────────────────
        DB::table('delivery_charge_by_areas')->where('branch_id', $branchId)->delete();

        // ──────────────────────────────────────────
        // 3. مناطق فلسطين مع أسعار التوصيل (₪)
        //    مجمّعة حسب المنطقة الجغرافية
        // ──────────────────────────────────────────
        $zones = [

            // ──── رام الله والبيرة (المركز) ────
            ['area_name' => 'رام الله',           'delivery_charge' => 10],
            ['area_name' => 'البيرة',              'delivery_charge' => 10],
            ['area_name' => 'بيتونيا',             'delivery_charge' => 12],
            ['area_name' => 'بير نبالا',           'delivery_charge' => 14],
            ['area_name' => 'قدورة',               'delivery_charge' => 12],
            ['area_name' => 'عين عريك',            'delivery_charge' => 15],
            ['area_name' => 'بيتيلو',              'delivery_charge' => 18],
            ['area_name' => 'دير قديس',            'delivery_charge' => 18],
            ['area_name' => 'أبو قش',              'delivery_charge' => 14],
            ['area_name' => 'بيت أنان',            'delivery_charge' => 20],
            ['area_name' => 'جفنا',                'delivery_charge' => 15],
            ['area_name' => 'دورا القرع',          'delivery_charge' => 20],

            // ──── القدس ────
            ['area_name' => 'القدس — البلدة القديمة', 'delivery_charge' => 25],
            ['area_name' => 'القدس — المشارف',     'delivery_charge' => 28],
            ['area_name' => 'القدس — بيت حنينا',   'delivery_charge' => 25],
            ['area_name' => 'القدس — شعفاط',       'delivery_charge' => 25],
            ['area_name' => 'القدس — رأس العامود',  'delivery_charge' => 25],
            ['area_name' => 'القدس — الطور',       'delivery_charge' => 27],
            ['area_name' => 'القدس — عناتا',       'delivery_charge' => 22],
            ['area_name' => 'العيزرية',             'delivery_charge' => 20],
            ['area_name' => 'أبو ديس',             'delivery_charge' => 20],
            ['area_name' => 'العيسوية',            'delivery_charge' => 25],

            // ──── بيت لحم ────
            ['area_name' => 'بيت لحم',             'delivery_charge' => 20],
            ['area_name' => 'بيت جالا',            'delivery_charge' => 22],
            ['area_name' => 'بيت ساحور',           'delivery_charge' => 22],
            ['area_name' => 'الدهيشة',             'delivery_charge' => 22],
            ['area_name' => 'الخضر',               'delivery_charge' => 25],
            ['area_name' => 'العبيدية',            'delivery_charge' => 25],
            ['area_name' => 'الولجة',              'delivery_charge' => 28],
            ['area_name' => 'حوسان',               'delivery_charge' => 28],
            ['area_name' => 'نحالين',              'delivery_charge' => 30],

            // ──── الخليل ────
            ['area_name' => 'الخليل — المركز',     'delivery_charge' => 30],
            ['area_name' => 'الخليل — الحرس',      'delivery_charge' => 32],
            ['area_name' => 'الخليل — الكيلو 9',   'delivery_charge' => 35],
            ['area_name' => 'الخليل — بلد',        'delivery_charge' => 30],
            ['area_name' => 'دورا',                'delivery_charge' => 35],
            ['area_name' => 'يطا',                 'delivery_charge' => 40],
            ['area_name' => 'سعير',                'delivery_charge' => 35],
            ['area_name' => 'حلحول',               'delivery_charge' => 32],
            ['area_name' => 'ترقومية',             'delivery_charge' => 38],
            ['area_name' => 'السموع',              'delivery_charge' => 45],
            ['area_name' => 'بني نعيم',            'delivery_charge' => 35],

            // ──── نابلس ────
            ['area_name' => 'نابلس — المركز',      'delivery_charge' => 25],
            ['area_name' => 'نابلس — شرق',         'delivery_charge' => 28],
            ['area_name' => 'نابلس — غرب',         'delivery_charge' => 28],
            ['area_name' => 'بلاطة البلد',         'delivery_charge' => 25],
            ['area_name' => 'رفيديا',              'delivery_charge' => 25],
            ['area_name' => 'حوارة',               'delivery_charge' => 30],
            ['area_name' => 'زواتا',               'delivery_charge' => 32],
            ['area_name' => 'بيت إيبا',            'delivery_charge' => 30],
            ['area_name' => 'صرة',                 'delivery_charge' => 30],

            // ──── جنين ────
            ['area_name' => 'جنين — المركز',       'delivery_charge' => 30],
            ['area_name' => 'جنين — مخيم',         'delivery_charge' => 30],
            ['area_name' => 'يعبد',                'delivery_charge' => 38],
            ['area_name' => 'قباطية',              'delivery_charge' => 35],
            ['area_name' => 'عرابة',               'delivery_charge' => 35],
            ['area_name' => 'صانور',               'delivery_charge' => 40],
            ['area_name' => 'برقين',               'delivery_charge' => 35],

            // ──── طولكرم ────
            ['area_name' => 'طولكرم — المركز',     'delivery_charge' => 28],
            ['area_name' => 'طولكرم — مخيم',       'delivery_charge' => 28],
            ['area_name' => 'عنبتا',               'delivery_charge' => 32],
            ['area_name' => 'قفين',                'delivery_charge' => 35],
            ['area_name' => 'بلعا',                'delivery_charge' => 35],
            ['area_name' => 'إيلار',               'delivery_charge' => 38],

            // ──── قلقيلية ────
            ['area_name' => 'قلقيلية — المركز',    'delivery_charge' => 28],
            ['area_name' => 'حبلة',                'delivery_charge' => 30],
            ['area_name' => 'كفر ثلث',             'delivery_charge' => 32],
            ['area_name' => 'بكا الشرقية',         'delivery_charge' => 32],

            // ──── سلفيت ────
            ['area_name' => 'سلفيت — المركز',      'delivery_charge' => 25],
            ['area_name' => 'كفل حارس',            'delivery_charge' => 28],
            ['area_name' => 'مردا',                'delivery_charge' => 28],
            ['area_name' => 'دير استيا',           'delivery_charge' => 30],

            // ──── أريحا والأغوار ────
            ['area_name' => 'أريحا — المركز',      'delivery_charge' => 25],
            ['area_name' => 'أريحا — عين السلطان', 'delivery_charge' => 28],
            ['area_name' => 'الأغوار الشمالية',    'delivery_charge' => 40],
            ['area_name' => 'الأغوار الجنوبية',    'delivery_charge' => 45],
            ['area_name' => 'العوجا',              'delivery_charge' => 35],

            // ──── طوباس ────
            ['area_name' => 'طوباس — المركز',      'delivery_charge' => 35],
            ['area_name' => 'تياسير',              'delivery_charge' => 45],

            // ──── الداخل الفلسطيني (48) ────
            ['area_name' => 'حيفا',                'delivery_charge' => 50],
            ['area_name' => 'عكا',                 'delivery_charge' => 55],
            ['area_name' => 'الناصرة',             'delivery_charge' => 45],
            ['area_name' => 'أم الفحم',            'delivery_charge' => 40],
            ['area_name' => 'طمرة',                'delivery_charge' => 45],
            ['area_name' => 'باقة الغربية',        'delivery_charge' => 38],
            ['area_name' => 'جلجولية',             'delivery_charge' => 35],
            ['area_name' => 'كفر قاسم',            'delivery_charge' => 35],
            ['area_name' => 'يافا',                'delivery_charge' => 50],
            ['area_name' => 'اللد',                'delivery_charge' => 48],
            ['area_name' => 'الرملة',              'delivery_charge' => 48],
            ['area_name' => 'تل أبيب',             'delivery_charge' => 55],
            ['area_name' => 'بئر السبع',           'delivery_charge' => 60],
            ['area_name' => 'عسقلان — المجدل',     'delivery_charge' => 60],
        ];

        // ──────────────────────────────────────────
        // 4. أدخل المناطق
        // ──────────────────────────────────────────
        $now  = now();
        $rows = array_map(fn($z) => [
            'branch_id'       => $branchId,
            'area_name'       => $z['area_name'],
            'delivery_charge' => $z['delivery_charge'],
            'created_at'      => $now,
            'updated_at'      => $now,
        ], $zones);

        // أدخل على دفعات لتجنب استعلام ضخم واحد
        foreach (array_chunk($rows, 20) as $chunk) {
            DB::table('delivery_charge_by_areas')->insert($chunk);
        }

        $this->command->info('✅ تم إضافة ' . count($zones) . ' منطقة توصيل في فلسطين');
        $this->command->info('   نوع التوصيل: حسب المنطقة (area-wise)');
        $this->command->info('   أرخص منطقة : رام الله / البيرة — 10 ₪');
        $this->command->info('   أغلى منطقة : بئر السبع / تل أبيب — 55-60 ₪');
    }
}
