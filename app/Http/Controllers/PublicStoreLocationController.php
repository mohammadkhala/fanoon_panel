<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class PublicStoreLocationController extends Controller
{
    public function __invoke(): View
    {
        $raw = Helpers::get_business_settings('store_google_maps_url');
        $storeGoogleMapsUrl = is_string($raw) ? trim($raw) : '';
        $mapPreviewEmbedSrc = Helpers::googleMapsStoreUrlToEmbedSrc($storeGoogleMapsUrl);

        return view('public.store-location', [
            'storeGoogleMapsUrl' => $storeGoogleMapsUrl,
            'mapPreviewEmbedSrc' => $mapPreviewEmbedSrc,
        ]);
    }
}
