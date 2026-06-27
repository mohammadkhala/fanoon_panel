<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Branch;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * يضيف مدناً وبلدات عربية داخل الخط الأخضر ويربطها بمنطقة توصيل «عرب الداخل».
 * تشغيل: php artisan db:seed --class=InsideArabTownsCitiesSeeder
 */
class InsideArabTownsCitiesSeeder extends Seeder
{
    private const AREA_NAME_AR = 'عرب الداخل';

    private const AREA_NAME_EN = 'Arab towns (Israel)';

    /** أسماء عربية شائعة للتجمعات داخل الخط الأخضر — يمكن توسيعها من لوحة التحكم. */
    private const CITY_NAMES = [
        'أبو سنان',
        'أم الفحم',
        'إكسال',
        'البعنة',
        'الرملة',
        'الطيبة',
        'اللد',
        'المغار',
        'الناصرة',
        'باقة الغربية',
        'تل السبع',
        'جسر الزرقاء',
        'جولس',
        'حيفا',
        'حورة',
        'دالية الكرمل',
        'دير الأسد',
        'دير حنا',
        'رهط',
        'سخنين',
        'شفاعمرو',
        'عسفيا',
        'عرابة',
        'عكا',
        'عيلبون',
        'فسوطة',
        'قلنسوة',
        'كابول',
        'كفر ياسيف',
        'كفركنا',
        'لكية',
        'طمرة',
        'يافا',
        'يانوح جت',
    ];

    public function run(): void
    {
        $branch = Branch::query()->orderBy('id')->first();
        if (!$branch) {
            $this->command->error('لا يوجد فرع — أضف فرعاً أولاً.');
            return;
        }

        if (!Schema::hasTable('areas') || !Schema::hasColumn('cities', 'area_id')) {
            $this->command->error('جدول areas أو عمود cities.area_id غير متوفر.');
            return;
        }

        $area = Area::query()
            ->where('branch_id', $branch->id)
            ->where(function ($q) {
                $q->where('name_ar', self::AREA_NAME_AR)
                    ->orWhere('name_en', self::AREA_NAME_EN)
                    ->orWhere('name_ar', 'like', '%عرب الداخل%');
            })
            ->first();

        if (!$area) {
            $maxOrder = (int) Area::where('branch_id', $branch->id)->max('sort_order');
            $area = Area::create([
                'branch_id' => $branch->id,
                'name_en' => self::AREA_NAME_EN,
                'name_ar' => self::AREA_NAME_AR,
                'names' => ['ar' => self::AREA_NAME_AR, 'en' => self::AREA_NAME_EN],
                'delivery_charge' => 20.0,
                'sort_order' => $maxOrder + 1,
            ]);
            $this->syncDeliveryChargeByArea($branch->id, $area->name_en, 20.0);
            $this->command->info('تم إنشاء منطقة التوصيل: ' . self::AREA_NAME_AR);
        } else {
            $this->command->info('استخدام منطقة موجودة: ' . ($area->name_ar ?: $area->name_en));
        }

        $baseOrder = (int) City::where('area_id', $area->id)->max('sort_order');
        $added = 0;
        $skipped = 0;

        foreach (self::CITY_NAMES as $name) {
            $exists = City::query()
                ->where('area_id', $area->id)
                ->where(function ($q) use ($name) {
                    $q->where('name_ar', $name)
                        ->orWhere('name', $name);
                })
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            City::create([
                'area_id' => $area->id,
                'name' => $name,
                'name_ar' => $name,
                'names' => ['ar' => $name, 'en' => $name],
                'sort_order' => ++$baseOrder,
            ]);
            $added++;
        }

        $this->command->info("تمت إضافة {$added} مدينة/بلدة، وتخطّي {$skipped} موجودة مسبقاً في نفس المنطقة.");
    }

    private function syncDeliveryChargeByArea(int $branchId, string $areaNameEn, float $charge): void
    {
        if (!Schema::hasTable('delivery_charge_by_areas')) {
            return;
        }
        $exists = DB::table('delivery_charge_by_areas')
            ->where('branch_id', $branchId)
            ->where('area_name', $areaNameEn)
            ->exists();
        if ($exists) {
            return;
        }
        $now = now();
        DB::table('delivery_charge_by_areas')->insert([
            'branch_id' => $branchId,
            'area_name' => $areaNameEn,
            'delivery_charge' => $charge,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
