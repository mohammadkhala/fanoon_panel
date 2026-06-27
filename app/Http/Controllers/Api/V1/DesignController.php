<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    /**
     * POST /api/v1/design/upload
     *
     * يستقبل صورة base64 من محرر Fabric.js ويحفظها في storage/app/public/designs/
     * ويعيد الرابط العام للصورة.
     *
     * Body (JSON):
     *   image: "data:image/png;base64,..."  (مطلوب)
     *   product_id: int  (اختياري)
     */
    public function upload(Request $request)
    {
        $dataUrl = $request->input('image');

        if (empty($dataUrl)) {
            return response()->json(['errors' => ['image' => ['الحقل مطلوب']]], 422);
        }

        // فصل الـ header عن البيانات
        // format: data:image/png;base64,<data>
        if (!preg_match('/^data:(image\/(?:png|jpeg|webp));base64,(.+)$/i', $dataUrl, $m)) {
            return response()->json(['errors' => ['image' => ['صيغة غير صالحة، المقبول: PNG, JPEG, WEBP']]], 422);
        }

        $mimeType  = strtolower($m[1]);  // image/png
        $b64Data   = $m[2];
        $binaryData = base64_decode($b64Data, strict: true);

        if ($binaryData === false) {
            return response()->json(['errors' => ['image' => ['بيانات base64 غير صالحة']]], 422);
        }

        // حد الحجم: 5 MB
        if (strlen($binaryData) > 5 * 1024 * 1024) {
            return response()->json(['errors' => ['image' => ['حجم الصورة يتجاوز 5 MB']]], 422);
        }

        $ext      = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            default       => 'png',
        };

        $filename  = 'designs/' . date('Y/m') . '/' . Str::uuid() . '.' . $ext;
        Storage::disk('public')->put($filename, $binaryData);

        $url = Storage::disk('public')->url($filename);

        return response()->json([
            'url'        => $url,
            'filename'   => $filename,
            'product_id' => $request->integer('product_id') ?: null,
        ], 201);
    }
}
