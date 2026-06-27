<div class="modal-header border-0 pb-0">
    <h5 class="modal-title">{{ $coupon->title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body pt-0">
<div class="coupon__details">
    <div class="coupon__details-body">
        <div class="coupon__details-header">
            <h6 class="coupon__title" id="title">{{ $coupon->title }}</h6>
            <div class="coupon__discount-badge">
                <span id="discount">{{ $coupon->discount_type == 'amount' ? Helpers::set_symbol($coupon->discount) : $coupon->discount . '%' }}</span>
                <small>{{ translate('discount') }}</small>
            </div>
        </div>
        <div class="coupon__details-grid">
            <div class="coupon__detail-row">
                <span class="coupon__label">{{ translate('code') }}</span>
                <strong id="coupon_code">{{ $coupon->code }}</strong>
            </div>
            <div class="coupon__detail-row">
                <span class="coupon__label">{{ translate('coupon') }} {{ translate('type') }}</span>
                <span>{{ translate(str_replace('_', ' ', $coupon->coupon_type)) }}</span>
            </div>
            <div class="coupon__detail-row">
                <span class="coupon__label">{{ translate('minimum_purchase') }}</span>
                <strong id="min_purchase">{{ Helpers::set_symbol($coupon->min_purchase) }}</strong>
            </div>
            @if($coupon->coupon_type != 'free_delivery' && $coupon->discount_type == 'percent')
            <div class="coupon__detail-row">
                <span class="coupon__label">{{ translate('maximum_discount') }}</span>
                <strong id="max_discount">{{ Helpers::set_symbol($coupon->max_discount) }}</strong>
            </div>
            @endif
            <div class="coupon__detail-row">
                <span class="coupon__label">{{ translate('start_date') }}</span>
                <span id="start_date">{{ \Carbon\Carbon::parse($coupon->start_date)->format('d/m/Y') }}</span>
            </div>
            <div class="coupon__detail-row">
                <span class="coupon__label">{{ translate('expire_date') }}</span>
                <span id="expire_date">{{ \Carbon\Carbon::parse($coupon->expire_date)->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>
</div>
</div>
