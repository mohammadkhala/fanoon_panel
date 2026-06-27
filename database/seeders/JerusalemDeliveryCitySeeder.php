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
 * يضمن وجود منطقة توصيل «القدس» ومدينة «القدس» تحتها.
 * تشغيل: php artisan db:seed --class=JerusalemDeliveryCitySeeder
 */
class JerusalemDeliveryCitySeeder extends Seeder
{
    private const AREA_NAME_AR = 'القدس';

    private const AREA_NAME_EN = 'Jerusalem';

    private const CITY_NAME = 'القدس';

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
            ->where('name_ar', self::AREA_NAME_AR)
            ->first()
            ?? Area::query()
                ->where('branch_id', $branch->id)
                ->where('name_en', self::AREA_NAME_EN)
                ->first();

        if (!$area) {
            $maxOrder = (int) Area::where('branch_id', $branch->id)->max('sort_order');
            $area = Area::create([
                'branch_id' => $branch->id,
                'name_en' => self::AREA_NAME_EN,
                'name_ar' => self::AREA_NAME_AR,
                'names' => ['ar' => self::AREA_NAME_AR, 'en' => self::AREA_NAME_EN],
                'delivery_charge' => 25.0,
                'sort_order' => $maxOrder + 1,
            ]);
            $this->syncDeliveryChargeByArea($branch->id, $area->name_en, 25.0);
            $this->command->info('تم إنشاء منطقة التوصيل: ' . self::AREA_NAME_AR);
        } else {
            $this->command->info('استخدام منطقة موجودة: ' . ($area->name_ar ?: $area->name_en));
        }

        $exists = City::query()
            ->where('area_id', $area->id)
            ->where(function ($q) {
                $q->where('name_ar', self::CITY_NAME)
                    ->orWhere('name', self::CITY_NAME);
            })
            ->exists();

        if ($exists) {
            $this->command->info('المدينة «' . self::CITY_NAME . '» موجودة مسبقاً ضمن هذه المنطقة.');
            return;
        }

        $baseOrder = (int) City::where('area_id', $area->id)->max('sort_order');

        City::create([
            'area_id' => $area->id,
            'name' => self::CITY_NAME,
            'name_ar' => self::CITY_NAME,
            'names' => ['ar' => self::CITY_NAME, 'en' => self::CITY_NAME],
            'sort_order' => $baseOrder + 1,
        ]);

        $this->command->info('تمت إضافة المدينة «' . self::CITY_NAME . '» ضمن منطقة «' . ($area->name_ar ?: self::AREA_NAME_AR) . '».');
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
