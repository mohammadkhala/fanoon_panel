<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    /**
     * Store a contact us message from app/website.
     * Throttled via route (e.g. 10/min per IP).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be valid',
            'message.required' => 'Message is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        ContactUs::create($validator->validated());
        Cache::forget('admin_store_data');

        return response()->json([
            'message' => translate('Your message has been sent successfully'),
            'success' => 1,
        ], 201);
    }
}
