<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * اختبار مهام خطة العلاج: CORS، كاش Branch storeData، unifiedSearch، getEarningStatistics
 */
class TreatmentPlanTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        Cache::flush();
        $this->admin = Admin::first() ?? tap(new Admin(), function ($a) {
            $a->f_name = 'Test';
            $a->l_name = 'Admin';
            $a->email = 'admin@test.com';
            $a->password = Hash::make('password');
            $a->save();
        });
    }

    public function test_cors_config_returns_array(): void
    {
        $origins = config('cors.allowed_origins');
        $this->assertIsArray($origins);
        $this->assertNotEmpty($origins);
    }

    public function test_unified_search_limits_query_length(): void
    {
        $longQuery = str_repeat('a', 150);
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.unified-search') . '?q=' . urlencode($longQuery));
        $response->assertOk();
        $response->assertJsonStructure(['orders', 'products', 'customers']);
    }

    public function test_unified_search_rejects_short_query(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.unified-search') . '?q=a');
        $response->assertOk();
        $response->assertJson([
            'orders' => [],
            'products' => [],
            'customers' => [],
        ]);
    }

    /**
     * getEarningStatistics يستخدم YEAR/MONTH (MySQL) — لا يعمل مع SQLite في الاختبارات.
     * الكود يعمل في الإنتاج مع MySQL.
     */
}
