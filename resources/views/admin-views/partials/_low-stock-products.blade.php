<div class="card-header">
    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
        <img width="20" src="{{asset('assets/admin/img/icons/top-selling-product.png')}}" alt="{{ translate('image') }}">
        {{translate('low_stock_products')}}
    </h4>
</div>

<div class="card-body">
    <div class="d-flex flex-column gap-3">
        @forelse($low_stock_products ?? [] as $item)
            <a class="d-flex flex-wrap align-items-center justify-content-between gap-3" href="{{route('admin.product.view',[$item->id])}}">
                <div class="media align-items-center gap-3">
                    <div class="avatar-lg">
                        <img class="rounded border img-fit"
                             src="{{$item->image_fullpath[0] ?? asset('assets/admin/img/160x160/img2.jpg')}}"
                             alt="{{$item->name}}-image">
                    </div>
                    <div class="media-body">
                        <span class="text-dark">{{substr($item->name ?? '',0,20)}}{{strlen($item->name ?? '')>20?'...':''}}</span>
                    </div>
                </div>
                <label class="px-2 py-1 bg-warning text-dark rounded lh-1.3">{{translate("stock")}}: {{$item->total_stock ?? 0}}</label>
            </a>
        @empty
            <p class="text-muted mb-0">{{translate('no_low_stock_products')}}</p>
        @endforelse
    </div>
</div>
