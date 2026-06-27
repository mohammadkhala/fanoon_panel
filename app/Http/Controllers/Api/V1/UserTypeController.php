<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserType;
use Illuminate\Http\JsonResponse;

class UserTypeController extends Controller
{
    /**
     * List user types for registration / app (no auth required).
     */
    public function index(): JsonResponse
    {
        $types = UserType::orderBy('id')->get(['id', 'name', 'is_default']);
        return response()->json($types, 200);
    }
}
