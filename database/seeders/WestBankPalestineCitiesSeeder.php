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
 * يضيف مدن الضفة الغربية ويربطها بمنطقة توصيل «الضفة الغربية».
 * تشغيل: php artisan db:seed --class=WestBankPalestineCitiesSeeder
 */
class WestBankPalestineCitiesSeeder extends Seeder
{
    private const AREA_NAME_AR = 'الضفة الغربية';

    private const AREA_NAME_EN = 'West Bank';

    /** أسماء فريدة بالترتيب (بدون عناوين أقسام مكررة من القائمة الأصلية). */
    private const CITY_NAMES = [
        'أبو ديس',
        'العيزرية',
        'رام الله',
        'البيرة',
        'البدو',
        'بيتونيا',
        'بيت لقيا',
        'بيت أمر',
        'بيت جالا',
        'بيت ساحور',
        'بيت لحم',
        'نابلس',
        'طوباس',
        'طولكرم',
        'قلقيلية',
        'يطا',
        'دورا',
        'حلحول',
        'الخليل',
        'الظاهرية',
        'سعير',
        'أريحا',
        'جنين',
        'سلفيت',
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
                    ->orWhere('name_ar', 'like', '%ضفة الغربية%');
            })
            ->first();

        if (!$area) {
            $maxOrder = (int) Area::where('branch_id', $branch->id)->max('sort_order');
            $area = Area::create([
                'branch_id' => $branch->id,
                'name_en' => self::AREA_NAME_EN,
                'name_ar' => self::AREA_NAME_AR,
                'names' => ['ar' => self::AREA_NAME_AR, 'en' => self::AREA_NAME_EN],
                'delivery_charge' => 15.0,
                'sort_order' => $maxOrder + 1,
            ]);
            $this->syncDeliveryChargeByArea($branch->id, $area->name_en, 15.0);
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

        $this->command->info("تمت إضافة {$added} مدينة، وتخطّي {$skipped} موجودة مسبقاً في نفس المنطقة.");
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
