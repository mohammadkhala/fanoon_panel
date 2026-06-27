<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    public function __construct(
        private Tag $tag
    ) {}

    /**
     * List tags for client (id, name). Used for product filter.
     */
    public function apiList(): JsonResponse
    {
        $tags = $this->tag
            ->with(['translations' => fn ($q) => $q->where('locale', app()->getLocale())])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Tag $t) => ['id' => $t->id, 'name' => $t->name]);

        return response()->json($tags, 200);
    }
}
