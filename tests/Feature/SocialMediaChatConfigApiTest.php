<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * إعدادات الدردشة (واتساب / تيليجرام / ماسنجر) من لوحة التحكم
 * تظهر في GET /api/v1/config للمتجر.
 */
class SocialMediaChatConfigApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\BaitPaitSeeder::class);
    }

    public function test_config_always_returns_whatsapp_telegram_messenger_shapes(): void
    {
        Cache::forget('api_v1_configuration_payload_v1');

        $response = $this->getJson('/api/v1/config');

        $response->assertOk();
        $response->assertJsonPath('whatsapp.status', 0);
        $response->assertJsonPath('whatsapp.number', '');
        $response->assertJsonPath('telegram.status', 0);
        $response->assertJsonPath('telegram.user_name', '');
        $response->assertJsonPath('messenger.status', 0);
        $response->assertJsonPath('messenger.user_name', '');
    }

    public function test_config_reflects_saved_chat_settings(): void
    {
        BusinessSetting::updateOrCreate(
            ['key' => 'whatsapp'],
            ['value' => json_encode(['status' => 1, 'number' => '970599123456'])]
        );
        BusinessSetting::updateOrCreate(
            ['key' => 'telegram'],
            ['value' => json_encode(['status' => 1, 'user_name' => 'elitevape_support'])]
        );
        Cache::forget(CACHE_BUSINESS_SETTINGS_TABLE);
        Cache::forget('api_v1_configuration_payload_v1');

        $response = $this->getJson('/api/v1/config');

        $response->assertOk();
        $response->assertJsonPath('whatsapp.status', 1);
        $response->assertJsonPath('whatsapp.number', '970599123456');
        $response->assertJsonPath('telegram.status', 1);
        $response->assertJsonPath('telegram.user_name', 'elitevape_support');
    }
}
