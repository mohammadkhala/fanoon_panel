<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\BusinessSetting;

class PageController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSetting
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $lang = $this->getRequestLang();

        $returnPage = $this->businessSetting->where(['key' => 'return_page'])->first();
        $refundPage = $this->businessSetting->where(['key' => 'refund_page'])->first();
        $cancellationPage = $this->businessSetting->where(['key' => 'cancellation_page'])->first();

        return response()->json([
            'return_page' => Helpers::parsePageContentWithStatus($returnPage->value ?? null, $lang, 'ar'),
            'refund_page' => Helpers::parsePageContentWithStatus($refundPage->value ?? null, $lang, 'ar'),
            'cancellation_page' => Helpers::parsePageContentWithStatus($cancellationPage->value ?? null, $lang, 'ar'),
        ]);
    }

    private function getRequestLang(): string
    {
        $lang = request()->header('X-localization') ?? explode(',', request()->header('Accept-Language', 'ar'))[0] ?? 'ar';
        return preg_match('/^(ar|en|he)/', trim($lang), $m) ? $m[1] : 'ar';
    }
}
