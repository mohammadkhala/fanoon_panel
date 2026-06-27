<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    public function __construct(
        private Banner $banner
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * Optional ?placement= filter:
     *   'main'      → banners explicitly placed as 'main' OR with no placement set (legacy default)
     *   any other   → exact match (e.g. 'hero_grid')
     * No placement param → returns every active banner (unchanged default behavior).
     */
    public function getBanners(Request $request): JsonResponse
    {
        $banners = Cache::rememberForever(CACHE_BANNER_TABLE, function () {
            return $this->banner->active()->latest()->get();
        });

        if ($request->filled('placement')) {
            $placement = $request->query('placement');
            $banners = $banners->filter(function ($b) use ($placement) {
                if ($placement === 'main') {
                    return empty($b->placement) || $b->placement === 'main';
                }
                return $b->placement === $placement;
            })->values();
        }

        return response()->json($banners, 200);
    }
}
