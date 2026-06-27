<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CategoryLogic;
use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function __construct(
        private Category $category
    )
    {
    }

    /**
     * Return root categories visible to the current caller (auth or guest).
     *
     * Category visibility rules (visible_to_user_types column):
     *   NULL        → visible to everyone
     *   []          → hidden from everyone
     *   [0, ...]    → 0 = guest; any positive integer = user_type_id
     */
    public function getCategories(): JsonResponse
    {
        $locale   = app()->getLocale();
        $cacheKey = CACHE_CATEGORY_TABLE . '_' . $locale;

        // Fetch all active root categories (cached, no per-user branching in DB)
        $categories = Cache::rememberForever($cacheKey, function () {
            return $this->category->where(['parent_id' => 0, 'status' => 1])->orderBy('position')->get();
        });

        // ── Determine caller identity ─────────────────────────────────
        $user        = auth('api')->user();
        $isGuest     = $user === null;
        $userTypeId  = $user?->user_type_id;   // null if user has no assigned type

        // ── Filter by visibility ──────────────────────────────────────
        $categories = $categories->filter(function ($category) use ($isGuest, $userTypeId) {
            $allowed = $category->visible_to_user_types; // already cast to array|null

            // NULL → visible to everyone
            if ($allowed === null) {
                return true;
            }

            // Empty array → hidden from everyone
            if (empty($allowed)) {
                return false;
            }

            // Guest visitors: must have 0 in the allowed list
            if ($isGuest) {
                return in_array(0, $allowed);
            }

            // Logged-in user with no assigned type → no restriction
            if ($userTypeId === null) {
                return true;
            }

            // Logged-in user with a type → must be in the allowed list
            return in_array((int) $userTypeId, $allowed);
        })->values(); // re-index

        foreach ($categories as $category) {
            $category['products_count'] = Product::whereJsonContains('category_ids', ['id' => (string)$category['id']])->count();
        }

        return response()->json($categories, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getChildes($id): JsonResponse
    {
        $cacheKey = CACHE_CATEGORY_CHILDREN . '_' . $id;
        $categories = Cache::remember($cacheKey, now()->addHours(1), function () use ($id) {
            return $this->category->where(['parent_id' => $id, 'status' => 1])->get();
        });
        return response()->json($categories, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getProducts($id): JsonResponse
    {
        $products = Helpers::filter_visible_products(CategoryLogic::products($id), true);
        $products = Helpers::product_data_formatting($products, true);
        $products = Helpers::apply_user_type_prices_to_products($products, true);
        return response()->json($products, 200);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getAllProducts($id): JsonResponse
    {
        try {
            $products = Helpers::filter_visible_products(CategoryLogic::all_products($id), true);
            $products = Helpers::product_data_formatting($products, true);
            $products = Helpers::apply_user_type_prices_to_products($products, true);
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getFeaturedCategories(): JsonResponse
    {
        // ملاحظة: لا نُخزّن الرؤية/السعر الخاص بنوع العميل داخل الكاش — هذه القيم
        // تختلف من عميل لآخر، فنُخزّن فقط القائمة الخام ونُطبّق الفلترة/التسعير بعد القراءة من الكاش.
        $cacheKey = CACHE_FEATURED_CATEGORIES . '_raw_' . app()->getLocale();
        $rawFeaturedData = Cache::remember($cacheKey, now()->addMinutes(30), function () {
            $featuredCategoryList = Category::active()->where(['is_featured' => 1])->get();
            $data = [];
            foreach ($featuredCategoryList as $category) {
                $products = Product::active()->whereJsonContains('category_ids', ['id' => (string)$category->id])->take(15)->get();
                if ($products->count() > 0) {
                    $data[] = [
                        'category' => $category,
                        'products' => $products,
                    ];
                }
            }
            return $data;
        });

        $featuredData = [];
        foreach ($rawFeaturedData as $entry) {
            $products = Helpers::filter_visible_products($entry['products'], true);
            if (count($products) > 0) {
                $formatted = Helpers::product_data_formatting($products, true);
                $formatted = Helpers::apply_user_type_prices_to_products($formatted, true);
                $featuredData[] = [
                    'category' => $entry['category'],
                    'products' => $formatted,
                ];
            }
        }
        return response()->json(['featured_data' => $featuredData], 200);
    }
    public function getPopularCategories(): JsonResponse
    {
        $locale = app()->getLocale();
        $cacheKey = CACHE_POPULAR_CATEGORY_TABLE . '_' . $locale;

        $categories = Cache::rememberForever($cacheKey, function () {
            return $this->category->where(['parent_id' => 0, 'status' => 1, 'is_featured' => 1])->orderBy('position')->get();
        });
        return response()->json($categories, 200);
    }
}
