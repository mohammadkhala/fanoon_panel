<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * اختبار توحيد المصطلحات: restaurant → store
 * يتحقق من أن الـ routes مسجلة وأن التوجيه يعمل
 */
class TerminologyUnificationTest extends TestCase
{
    public function test_admin_get_store_data_route_is_registered(): void
    {
        $this->assertTrue(
            Route::has('admin.get-store-data'),
            'route admin.get-store-data يجب أن يكون مسجلاً'
        );
    }

    public function test_admin_get_restaurant_data_redirects(): void
    {
        $response = $this->get(route('admin.get-restaurant-data'));

        // إما redirect للـ login (غير مسجل) أو redirect لـ get-store-data
        $this->assertTrue($response->isRedirection(), 'يجب أن يعيد توجيه');
    }
}
