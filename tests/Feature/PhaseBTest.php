<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * اختبارات المرحلة ب: تقسيم العملاء، طباعة جماعية، بحث موحّد.
 */
class PhaseBTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->seedMinimalData();
        $this->admin = Admin::first() ?? tap(new Admin(), function ($a) {
            $a->f_name = 'Test';
            $a->l_name = 'Admin';
            $a->email = 'admin@test.com';
            $a->password = Hash::make('password');
            $a->save();
        });
    }

    private function seedMinimalData(): void
    {
        if (!Branch::first()) {
            $b = new Branch();
            $b->name = 'Test Branch';
            $b->email = 'branch@test.com';
            $b->password = bcrypt('password');
            $b->status = true;
            $b->save();
        }
        if (!UserType::first()) {
            UserType::create(['name' => 'Default', 'is_default' => 1]);
        }
    }

    public function test_unified_search_requires_auth(): void
    {
        $response = $this->get(route('admin.unified-search', ['q' => 'test']));
        $response->assertRedirect();
    }

    public function test_unified_search_returns_json(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.unified-search', ['q' => 'ab']));

        $response->assertStatus(200);
        $response->assertJsonStructure(['orders', 'products', 'customers']);
    }

    public function test_unified_search_short_query_returns_empty(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.unified-search', ['q' => 'a']));

        $response->assertStatus(200);
        $response->assertJson([
            'orders' => [],
            'products' => [],
            'customers' => [],
        ]);
    }

    public function test_customer_list_segment_filter(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.customer.list', ['segment' => 'vip']));

        $response->assertStatus(200);
    }

    public function test_order_list_page_has_bulk_print_elements(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.list', ['status' => 'all']));

        $response->assertStatus(200);
        $response->assertSee('btn-print-selected', false);
        $response->assertSee('order-checkbox', false);
    }
}
