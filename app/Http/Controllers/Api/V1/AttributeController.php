<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;

class AttributeController extends Controller
{
    public function __construct(
        private Attribute $attribute
    ) {}

    /**
     * List attributes for client (id, name). Used for product filter.
     */
    public function apiList(): JsonResponse
    {
        $attributes = $this->attribute
            ->withoutGlobalScopes()
            ->with(['translations' => fn ($q) => $q->where('locale', app()->getLocale())])
            ->orderBy('name')
            ->get()
            ->map(fn (Attribute $a) => ['id' => $a->id, 'name' => $a->name]);

        return response()->json($attributes, 200);
    }
}
