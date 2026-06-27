@extends('layouts.admin.app')

@section('title', translate('store_location_map'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('business_setup_image') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="card">
            <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div>
                    <h4 class="mb-0 d-flex align-items-center gap-2">
                        <i class="tio-map"></i>
                        {{ translate('store_location_map') }}
                    </h4>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <a href="{{ route('public.store-location') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">
                        <i class="tio-globe"></i> {{ translate('view_public_store_map') }}
                    </a>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#storeMapHelpModal" title="{{ translate('help_store_map_btn') }}">
                        <i class="tio-book-outlined"></i> {{ translate('help_store_map_btn') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.business-settings.update-store-location-map') }}" method="post">
                    @csrf
                    <div class="bg-light rounded p-3">
                        <div class="form-group mb-0">
                            <label class="input-label" for="store_google_maps_url">{{ translate('store_google_maps_url_label') }}</label>
                            <input type="text" name="store_google_maps_url" id="store_google_maps_url" class="form-control"
                                   dir="ltr"
                                   placeholder="https://www.google.com/maps/@..."
                                   value="{{ old('store_google_maps_url', $storeGoogleMapsUrl ?? '') }}"
                                   maxlength="2048"
                                   autocomplete="off">
                        </div>
                        <div id="store-map-preview-block" class="mt-4 pt-3 border-top">
                            <label class="input-label mb-2">{{ translate('store_google_maps_preview_title') }}</label>
                            <div id="store-map-preview-iframe-wrap" class="{{ !empty($mapPreviewEmbedSrc) ? '' : 'd-none' }}">
                                <iframe id="store-map-preview-iframe"
                                        title="{{ translate('store_google_maps_preview_title') }}"
                                        class="w-100 rounded"
                                        style="height: 320px; border: 1px solid #e7eaf3; min-height: 240px;"
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        src="{{ !empty($mapPreviewEmbedSrc) ? e($mapPreviewEmbedSrc) : '' }}"></iframe>
                            </div>
                            <p id="store-map-preview-msg-empty" class="text-muted small mb-0 {{ (!empty($mapPreviewEmbedSrc) || strlen(trim(old('store_google_maps_url', $storeGoogleMapsUrl ?? ''))) > 0) ? 'd-none' : '' }}">
                                {{ translate('store_google_maps_preview_empty') }}
                            </p>
                            <p id="store-map-preview-msg-no-embed" class="text-muted small mb-0 {{ (!empty($mapPreviewEmbedSrc) || strlen(trim(old('store_google_maps_url', $storeGoogleMapsUrl ?? ''))) === 0) ? 'd-none' : '' }}">
                                {{ translate('store_google_maps_preview_unavailable') }}
                            </p>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="reset" class="btn btn--reset min-w-120">{{ translate('reset') }}</button>
                            <button type="{{ config('app.mode') != 'demo' ? 'submit' : 'button' }}"
                                    class="btn btn-primary min-w-120 demo-form-submit">{{ translate('update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'storeMapHelpModal', 'titleKey' => 'help_store_map_title', 'pageKey' => 'help_store_map_page'])
@endsection

@push('script')
<script>
(function () {
    var input = document.getElementById('store_google_maps_url');
    if (!input) return;

    var wrap = document.getElementById('store-map-preview-iframe-wrap');
    var iframe = document.getElementById('store-map-preview-iframe');
    var msgEmpty = document.getElementById('store-map-preview-msg-empty');
    var msgNoEmbed = document.getElementById('store-map-preview-msg-no-embed');
    var form = input.closest('form');

    function buildOsmPreviewSrc(lat, lng, zoom) {
        var z = Math.max(1, Math.min(21, Math.round(zoom)));
        var latRad = lat * Math.PI / 180;
        var halfWidthM = 280;
        var halfHeightM = 220;
        var dLat = halfHeightM / 111320;
        var cosLat = Math.cos(latRad);
        var dLng = cosLat > 0.01 ? (halfWidthM / (111320 * cosLat)) : 0.2;
        var factor = Math.max(0.35, Math.min(2.5, 18 / Math.max(1, z)));
        dLat *= factor;
        dLng *= factor;
        var minLat = Math.max(-85, lat - dLat);
        var maxLat = Math.min(85, lat + dLat);
        var minLng = Math.max(-180, lng - dLng);
        var maxLng = Math.min(180, lng + dLng);
        var bbox = encodeURIComponent(minLng + ',' + minLat + ',' + maxLng + ',' + maxLat);
        var marker = encodeURIComponent(lat + ',' + lng);
        return 'https://www.openstreetmap.org/export/embed.html?bbox=' + bbox + '&layer=mapnik&marker=' + marker;
    }

    function buildEmbedSrc(raw) {
        if (!raw || !String(raw).trim()) return null;
        var noQuery = String(raw).trim().split('?')[0].split('#')[0];
        var m = noQuery.match(/@([-0-9.]+),([-0-9.]+),([0-9]+(?:\.[0-9]+)?)z/i);
        if (!m) return null;
        var lat = parseFloat(m[1], 10);
        var lng = parseFloat(m[2], 10);
        var zoom = Math.round(parseFloat(m[3], 10));
        if (isNaN(lat) || isNaN(lng) || isNaN(zoom)) return null;
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return null;
        return buildOsmPreviewSrc(lat, lng, zoom);
    }

    function updatePreview() {
        var v = input.value;
        var embed = buildEmbedSrc(v);
        var hasText = v.trim().length > 0;

        if (!hasText) {
            wrap.classList.add('d-none');
            msgEmpty.classList.remove('d-none');
            msgNoEmbed.classList.add('d-none');
            iframe.removeAttribute('src');
            return;
        }
        msgEmpty.classList.add('d-none');
        if (embed) {
            wrap.classList.remove('d-none');
            msgNoEmbed.classList.add('d-none');
            if (iframe.getAttribute('src') !== embed) {
                iframe.setAttribute('src', embed);
            }
        } else {
            wrap.classList.add('d-none');
            msgNoEmbed.classList.remove('d-none');
            iframe.removeAttribute('src');
        }
    }

    var debounceTimer = null;
    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(updatePreview, 350);
    });
    input.addEventListener('change', updatePreview);
    if (form) {
        form.addEventListener('reset', function () {
            setTimeout(updatePreview, 0);
        });
    }
    updatePreview();
})();
</script>
@endpush
