<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\UserType;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * اختبارات المرحلة ج: بحث بالخصائص (attributes).
 */
class PhaseCTest extends TestCase
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

    public function test_product_list_requires_auth(): void
    {
        $response = $this->get(route('admin.product.list'));
        $response->assertRedirect();
    }

    public function test_product_list_with_attribute_filter_returns_200(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.product.list', ['attribute_id' => 1]));

        $response->assertStatus(200);
    }

    public function test_product_list_attribute_filter_shows_attribute_select(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.product.list'));

        $response->assertStatus(200);
        $response->assertSee('name="attribute_id"', false);
    }

    public function test_product_list_attribute_filter_returns_matching_products(): void
    {
        $attrId = DB::table('attributes')->insertGetId(['name' => 'Color', 'created_at' => now(), 'updated_at' => now()]);
        $catId = (string) DB::table('categories')->insertGetId(['name' => 'Cat', 'parent_id' => 0, 'position' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]);

        $now = now();
        DB::table('products')->insert([
            [
                'name' => 'Product With Attr',
                'attributes' => json_encode([$attrId]),
                'category_ids' => json_encode([['id' => $catId, 'position' => 1]]),
                'image' => json_encode(['def.png']),
                'price' => 10,
                'total_stock' => 5,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Product Without Attr',
                'attributes' => json_encode([999]),
                'category_ids' => json_encode([['id' => $catId, 'position' => 1]]),
                'image' => json_encode(['def.png']),
                'price' => 10,
                'total_stock' => 5,
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.product.list', ['attribute_id' => $attrId]));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertCount(1, $products);
        $this->assertSame('Product With Attr', $products->first()->name);
    }
}
