<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['ar', 'ur', 'fa'], true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ translate('store_location_map') }} — {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif; margin: 0; padding: 1rem; background: #f4f6f8; color: #1a1a1a; }
        .wrap { max-width: 720px; margin: 0 auto; }
        h1 { font-size: 1.35rem; margin: 0 0 1rem; }
        .card { background: #fff; border-radius: 12px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        iframe { width: 100%; height: min(50vh, 400px); border: 1px solid #e2e8f0; border-radius: 8px; }
        .muted { color: #64748b; font-size: .95rem; line-height: 1.5; margin: 0 0 1rem; }
        .btn { display: inline-block; margin-top: .75rem; padding: .65rem 1.25rem; background: #2563eb; color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: 600; }
        .btn:hover { background: #1d4ed8; }
        [dir="rtl"] .btn { text-align: center; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>{{ translate('store_location_map') }}</h1>
    <div class="card">
        @if($storeGoogleMapsUrl === '')
            <p class="muted">{{ translate('store_google_maps_preview_empty') }}</p>
        @else
            @if(!empty($mapPreviewEmbedSrc))
                <iframe title="{{ translate('store_google_maps_preview_title') }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        src="{{ e($mapPreviewEmbedSrc) }}"></iframe>
            @else
                <p class="muted">{{ translate('store_google_maps_preview_unavailable') }}</p>
            @endif
            <a class="btn" href="{{ e($storeGoogleMapsUrl) }}" target="_blank" rel="noopener noreferrer">
                {{ translate('open_in_google_maps') }}
            </a>
        @endif
    </div>
</div>
</body>
</html>
