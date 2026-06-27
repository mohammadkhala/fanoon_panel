<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * وضع الصيانة في GET /api/v1/config يجب أن يعكس الإعدادات الحالية حتى مع وجود كاش للـ payload.
 */
class MaintenanceModeConfigApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\BaitPaitSeeder::class);
    }

    public function test_config_refreshes_maintenance_fields_when_payload_cache_is_warm(): void
    {
        Cache::forget('api_v1_configuration_payload_v1');
        Cache::forget(CACHE_BUSINESS_SETTINGS_TABLE);

        $first = $this->getJson('/api/v1/config');
        $first->assertOk();
        $first->assertJsonPath('maintenance_mode', false);

        BusinessSetting::updateOrCreate(
            ['key' => 'maintenance_mode'],
            ['value' => '1']
        );
        BusinessSetting::updateOrCreate(
            ['key' => 'maintenance_duration_setup'],
            ['value' => json_encode(['maintenance_duration' => 'until_change', 'start_date' => null, 'end_date' => null])]
        );
        BusinessSetting::updateOrCreate(
            ['key' => 'maintenance_system_setup'],
            ['value' => json_encode(['branch_panel', 'customer_app', 'web_app', 'deliveryman_app'])]
        );

        Cache::forget(CACHE_BUSINESS_SETTINGS_TABLE);

        $second = $this->getJson('/api/v1/config');
        $second->assertOk();
        $second->assertJsonPath('maintenance_mode', true);
        $second->assertJsonPath('advance_maintenance_mode.maintenance_status', 1);
    }
}
