<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DesignTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DesignTemplateController extends Controller
{
    public function getTemplates(): JsonResponse
    {
        $templates = Cache::rememberForever(CACHE_DESIGN_TEMPLATES, function () {
            return DesignTemplate::with('mainCategory.parent')
                ->active()
                ->orderBy('position')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($t) {
                    $cat = $t->mainCategory;
                    return [
                        'id'                 => $t->id,
                        'name'               => $t->name,
                        'product_id'         => $t->product_id,
                        'category_id'        => $t->category_id,
                        'category_name'      => $cat ? $cat->name : null,
                        'parent_category_id' => $cat ? ($cat->parent_id ?: null) : null,
                        'canvas_json'        => $t->canvas_json,
                        'canvas_width'       => $t->canvas_width,
                        'canvas_height'      => $t->canvas_height,
                        'thumbnail'          => $t->thumbnail_fullpath,
                    ];
                });
        });

        return response()->json($templates, 200);
    }
}
