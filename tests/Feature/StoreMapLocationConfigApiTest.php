<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StoreMapLocationConfigApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\BaitPaitSeeder::class);
    }

    public function test_config_includes_store_google_maps_url_and_alias(): void
    {
        Cache::forget('api_v1_configuration_payload_v1');
        Cache::forget(CACHE_BUSINESS_SETTINGS_TABLE);

        BusinessSetting::updateOrCreate(
            ['key' => 'store_google_maps_url'],
            ['value' => 'https://www.google.com/maps/@31.5,35.0,16z']
        );
        Cache::forget(CACHE_BUSINESS_SETTINGS_TABLE);
        Cache::forget('api_v1_configuration_payload_v1');

        $response = $this->getJson('/api/v1/config');
        $response->assertOk();
        $response->assertJsonPath('store_google_maps_url', 'https://www.google.com/maps/@31.5,35.0,16z');
        $response->assertJsonPath('google_maps_location_url', 'https://www.google.com/maps/@31.5,35.0,16z');
    }
}
