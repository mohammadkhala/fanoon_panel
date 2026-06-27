@extends('layouts.admin.app')
@section('title', translate('Order Details'))

@push('css_or_js')
@include('admin-views.partials._help-instructions-css')
@endpush

@section('content')
<div class="content container-fluid">
    <div class="mb-3 pb-xl-1 d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
            <img src="{{asset('assets/admin/img/icons/all_orders.png')}}" alt="{{ translate('order_details') }}">
            {{translate('order_details')}}
            <span class="badge badge-soft-dark rounded-50 fz-14">{{$order->details->count()}}</span>
        </h2>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#orderDetailsInstructionsModal">
                <i class="tio-book-outlined"></i> {{ translate('help_order_details_btn') }}
            </button>
            @if($order->type != 'pos' && in_array($order->order_status, ['pending', 'confirmed', 'processing']) && $order->payment_status == 'unpaid')
                <button type="button" class="btn btn--info offcanvas-trigger" data-target="#offcanvas__order_edit">
                    <i class="tio-edit"></i> {{ translate('Edit Order') }}
                </button>
            @endif

            <a class="btn btn-primary" target="_blank" href={{route('admin.orders.generate-invoice',[$order['id']])}}
               data-toggle="tooltip" data-placement="bottom" title="{{ translate('help_order_details_invoice') }}">
                <i class="tio-print"></i> {{translate('print_invoice')}}
            </a>
        </div>
    </div>

    @include('admin-views.partials._help-instructions-modal', ['modalId' => 'orderDetailsInstructionsModal', 'titleKey' => 'help_order_details_title', 'pageKey' => 'help_order_details_page'])

    @php($googleMapStatus = 0)
    <div class="row">
        <div class="col-lg-{{($order->user_id == null && $order->is_guest == 0) ? 12 : 8}} mb-3 mb-lg-0">
            <div class="card mb-3 mb-lg-5">
                <div class="card-body">
                    <div class="mb-3 text-dark d-print-none">
                        <div class="row gy-3">
                            <div class="col-sm-6">
                                <div class="d-flex flex-column justify-content-between h-100">
                                    <div class="d-flex flex-column gap-2">
                                        <h2 class="page-header-title">{{translate('order')}} #{{$order['id']}}</h2>
                                        <div>
                                            <i class="tio-date-range"></i>
                                            {{date('d M Y h:i a',strtotime($order['created_at']))}}
                                        </div>
                                        @if(!config('feature_flags.hide_branch_management', true))
                                        <h5 class="mb-0 flex-wrap gap-2">
                                            <i class="tio-shop"></i>
                                            {{translate('branch')}} : <label
                                                class="badge badge-secondary">{{$order->branch?$order->branch->name:'Branch deleted!'}}</label>
                                        </h5>
                                        @endif
                                    </div>

                                    @if($order['order_note'])
                                    <div><strong>{{translate('note')}}:</strong> {{$order['order_note']}}
                                    </div>
                                    @endif
                                    @if($order['bring_change_amount'] >0)
                                        <div
                                            class="badge badge-soft-info p-2 d-flex align-items-center gap-1 text-wrap text-left lh-1.3 font-size-sm mt-3">
                                            <i class="tio-info"></i>
                                            <span class="text-dark opacity-lg">
                                            {{translate('Please_bring').' '. \App\CentralLogics\Helpers::set_symbol($order['bring_change_amount']) . ' '.  translate('in_change_for_the_customer_when_making_the_delivery')}}
                                        </span>
                                        </div>
                                    @endif


                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex flex-column gap-3 align-items-sm-end h-100" style="font-size: 1rem;">
                                    <div class="d-flex flex-wrap align-items-center justify-content-sm-end gap-2 w-100">
                                        <span class="text-dark font-weight-medium" style="font-size: 1.05rem;">{{translate('Order_Status')}}</span>
                                        @if(in_array($order['order_status'], ['pending','confirmed']))
                                            <span class="badge badge-soft-info px-3 py-2 text-dark" style="font-size: 1rem;">{{ translate($order['order_status']) }}</span>
                                        @elseif(in_array($order['order_status'], ['processing','out_for_delivery']))
                                            <span class="badge badge-soft-warning px-3 py-2 text-dark" style="font-size: 1rem;">{{ $order['order_status']=='out_for_delivery' ? translate('Out_For_Delivery') : translate('processing') }}</span>
                                        @elseif($order['order_status']=='delivered')
                                            <span class="badge badge-soft-success px-3 py-2 text-dark" style="font-size: 1rem;">{{ translate('Delivered') }}</span>
                                        @elseif(in_array($order['order_status'], ['returned','failed','canceled']))
                                            <span class="badge badge-soft-danger px-3 py-2 text-dark" style="font-size: 1rem;">{{ $order['order_status']=='returned' ? translate('Returned') : ($order['order_status']=='failed' ? translate('Failed') : translate('canceled')) }}</span>
                                        @else
                                            <span class="badge badge-soft-secondary px-3 py-2 text-dark" style="font-size: 1rem;">{{ translate($order['order_status'] ?? '') }}</span>
                                        @endif
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center justify-content-sm-end gap-2 w-100">
                                        <span class="text-dark font-weight-medium" style="font-size: 1.05rem;">{{translate('payment_Method')}}</span>
                                        <span class="badge badge-soft-primary px-3 py-2 text-dark" style="font-size: 1rem;">{{ $order->payment_method == 'multiple' ? ucwords(implode(', ', $order->additional_payment_method)) : translate($order['payment_method']) }}</span>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center justify-content-sm-end gap-2 w-100">
                                        <span class="text-dark font-weight-medium" style="font-size: 1.05rem;">{{translate('Payment_Status')}}</span>
                                        <span class="badge badge-soft-{{ $order['payment_status'] == 'paid' ? 'success' : 'danger' }} px-3 py-2 text-dark" style="font-size: 1rem;">{{ $order['payment_status'] == 'paid' ? translate('paid') : translate('unpaid') }}</span>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center justify-content-sm-end gap-2 w-100">
                                        <span class="text-dark font-weight-medium" style="font-size: 1.05rem;">{{translate('order_Type')}}</span>
                                        <span class="badge badge-soft-secondary px-3 py-2 text-dark" style="font-size: 1rem;">{{ translate($order['order_type']) }}</span>
                                    </div>

                                    @if($order['payment_method'] != 'cash_on_delivery' && $order['payment_method'] != 'wallet')
                                    <div class="d-flex flex-wrap align-items-center justify-content-sm-end gap-2 w-100">
                                        <span class="text-dark font-weight-medium" style="font-size: 1.05rem;">{{translate('reference_Code')}}</span>
                                        @if($order['transaction_reference']==null && $order['order_type']!='pos')
                                        <button class="btn btn-outline-primary btn-sm py-1" data-toggle="modal" data-target=".bd-example-modal-sm" style="font-size: 1rem;">{{translate('add')}}</button>
                                        @elseif($order['order_type']!='pos')
                                        <span class="badge badge-soft-dark px-3 py-2 text-dark" style="font-size: 1rem;">{{ $order['transaction_reference'] }}</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @php($item_amount=0)
                    @php($sub_total=0)
                    @php($total_tax=0)
                    @php($total_dis_on_pro=0)
                    @php($total_item_discount=0)

                    <div class="table-responsive datatable-custom">
                        <table class="table table-hover table-border table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Item Description') }}</th>
                                    <th>{{ translate('Unit Price') }}</th>
                                    <th>{{ translate('Discount') }}</th>
                                    <th>{{ translate('Qty') }}</th>
                                    <th class="text-right">{{ translate('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $index => $detail)
                                @if($detail->product_details != null)
                                    @php($product = json_decode($detail->product_details, true))
                                    @php($op = null)
                                    @php($varRaw = $detail->variation ? json_decode($detail->variation, true) : [])
                                    @php($varArr = is_array($varRaw) ? $varRaw : [])
                                    @php($varShow = !empty($varArr) ? ($varArr[0] ?? $varArr) : [])
                                    @php($varShow = is_array($varShow) ? $varShow : [])
                                @else
                                    @php($product = null)
                                    @php($op = $orderedProducts[$index] ?? null)
                                    @php($varShow = [])
                                @endif
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="media gap-3 max-content">
                                            <div class="avatar-60 overflow-hidden rounded">
                                                @if($detail->product && $detail->product->image)
                                                    @php($imgList = $detail->product->image_fullpath ?? [])
                                                    <img class="img-fit" src="{{ (is_array($imgList) && !empty($imgList)) ? $imgList[0] : asset('assets/admin/img/160x160/img2.jpg') }}" alt="{{ translate('image') }}">
                                                @else
                                                    <img class="img-fit" src="{{ (($op ?? [])['image'] ?? asset('assets/admin/img/160x160/img2.jpg')) }}" alt="{{ translate('image') }}">
                                                @endif
                                            </div>
                                            <div class="media-body">
                                                <h6 class="mb-1 w-24ch">{{ (is_array($product) && isset($product['name'])) ? $product['name'] : (($op ?? [])['name'] ?? translate('Product deleted')) }}</h6>
                                                @if(!empty($varShow))
                                                    @foreach($varShow as $key1 => $variation)
                                                        <div class="font-size-sm text-body text-capitalize">
                                                            @if($variation != null)<span>{{ $key1 }} : </span>@endif
                                                            <span class="font-weight-bold">{{ $variation }}</span>
                                                        </div>
                                                    @endforeach
                                                @elseif($op && trim((string)(($op ?? [])['variant'] ?? '')) !== '')
                                                    <div class="font-size-sm text-body text-capitalize"><span class="font-weight-bold">{{ ($op ?? [])['variant'] }}</span></div>
                                                @endif

                                                {{-- عرض التصميم المخصص --}}
                                                @if(!empty($detail->design_image))
                                                <div class="mt-2">
                                                    <a href="{{ $detail->design_image }}" target="_blank"
                                                       title="عرض التصميم بالحجم الكامل">
                                                        <img src="{{ $detail->design_image }}"
                                                             alt="تصميم مخصص"
                                                             style="width:64px;height:64px;object-fit:cover;border-radius:8px;border:2px solid #10b46a;cursor:zoom-in">
                                                    </a>
                                                    <div class="mt-1">
                                                        <span class="badge badge-soft-success" style="font-size:11px">
                                                            <i class="tio tio-palette"></i> تصميم مخصص من العميل
                                                        </span>
                                                    </div>
                                                    @if(!empty($detail->design_json))
                                                    <div class="mt-2">
                                                        <a href="{{ route('admin.design-template.from-order', $detail->id) }}"
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="فتح التصميم في المحرّر للتعديل ثم إضافته كقالب">
                                                            <i class="tio tio-add"></i> إضافة كقالب
                                                        </a>
                                                    </div>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Helpers::set_symbol($detail['price']) }}</td>
                                    <td>{{ Helpers::set_symbol($detail['discount_on_product']) }}</td>
                                    <td>{{ $detail['quantity'] }}</td>
                                    <td class="text-right">
                                        @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                                        {{ Helpers::set_symbol($amount) }}
                                    </td>
                                </tr>
                                @php($item_amount+=$detail['price']*$detail['quantity'])
                                @php($sub_total+=$amount)
                                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
                                @php($total_item_discount += $detail['discount_on_product'] * $detail['quantity'])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-md-end mb-3 border-top pt-4">
                        <div class="col-md-9 col-lg-8 col-xl-7 col-xxl-5">
                            <dl class="row">
                                <dt class="col-6">{{translate('items')}} {{translate('price')}}:</dt>
                                <dd class="col-6 text-end">{{ Helpers::set_symbol($item_amount) }}</dd>

                                <dt class="col-6">{{translate('item_discount')}}:</dt>
                                <dd class="col-6 text-end">{{ Helpers::set_symbol($total_item_discount) }}</dd>

                                <dt class="col-6">{{translate('subtotal')}}:</dt>
                                <dd class="col-6 text-end">{{ Helpers::set_symbol($sub_total) }}</dd>
                                <dt class="col-6">{{translate('coupon')}} {{translate('discount')}}:</dt>
                                <dd class="col-6 text-end">
                                    - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>

                                @if(($order['loyalty_points_used'] ?? 0) > 0)
                                <dt class="col-6">{{translate('loyalty_discount')}} ({{ $order['loyalty_points_used'] }} {{translate('points')}}):</dt>
                                <dd class="col-6 text-end">
                                    - {{ Helpers::set_symbol($order['loyalty_discount_amount'] ?? 0) }}</dd>
                                @endif

                                @if($order['order_type'] == 'pos' || $order['order_type'] == 'pos_delivery')
                                <dt class="col-6">{{translate('Extra Discount')}}:</dt>
                                <dd class="col-6 text-end">
                                    - {{ Helpers::set_symbol($order['extra_discount']) }}</dd>
                                @endif
                                <dt class="col-6">{{translate('delivery')}} {{translate('fee')}}:</dt>
                                <dd class="col-6 text-end">
                                    {{ Helpers::set_symbol($deliveryChargeDisplay ?? $order['delivery_charge']) }}
                                </dd>

                                <dt class="col-6 border-top pt-2 font-weight-bold">{{translate('total')}}:</dt>
                                <dd class="col-6 text-end border-top pt-2 font-weight-bold">
                                    {{ Helpers::set_symbol($order['order_amount']) }}
                                </dd>
                                @if(($order['order_type'] == 'pos' || $order['order_type'] == 'pos_delivery') && $order['paid_amount'] >0)
                                    @if($order->payment_method == 'multiple')
                                        @foreach($order->additional_payment_amount as $key => $value)
                                            <dt class="col-6">{{ ucwords($key) . ' ' . translate('_payment')}}:</dt>
                                            <dd class="col-6 text-end"> {{ Helpers::set_symbol($value) }}</dd>
                                        @endforeach
                                    @else
                                        <dt class="col-6">{{translate('paid_amount')}}:</dt>
                                        <dd class="col-6 text-end"> {{ Helpers::set_symbol($order['paid_amount']) }}</dd>
                                    @endif
                                <dt class="col-6">{{translate('change_amount')}}:</dt>
                                <dd class="col-6 text-end">
                                    {{ Helpers::set_symbol($order['paid_amount']- $order['order_amount']) }}
                                </dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            @if($order['order_type'] != 'pos' && isset($shippingCompanies))
            <div class="card mb-3 mt-3">
                <div class="card-header">
                    <h4 class="card-header-title"><i class="tio-truck"></i> {{ translate('shipping') }}</h4>
                </div>
                <div class="card-body">
                    @forelse($order->orderShipments ?? [] as $shipment)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <strong>{{ $shipment->shippingCompany?->name ?? '-' }}</strong>
                                @if($shipment->tracking_number)
                                    <span class="text-muted"> — {{ translate('tracking') }}: {{ $shipment->tracking_number }}</span>
                                @endif
                                <span class="badge badge-soft-{{ $shipment->status == 'delivered' ? 'success' : ($shipment->status == 'failed' ? 'danger' : 'info') }} ml-1">{{ $shipment->status }}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-soft-primary" data-toggle="modal" data-target="#editShipmentModal-{{ $shipment->id }}"><i class="tio-edit"></i></button>
                        </div>
                        <div class="modal fade" id="editShipmentModal-{{ $shipment->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.orders.update-shipment', ['shipmentId' => $shipment->id]) }}" method="post">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ translate('edit') }} {{ translate('shipment') }}</h5>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>{{ translate('tracking_number') }}</label>
                                                <input type="text" name="tracking_number" class="form-control" value="{{ $shipment->tracking_number }}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ translate('status') }}</label>
                                                <select name="status" class="form-control">
                                                    <option value="pending" {{ $shipment->status == 'pending' ? 'selected' : '' }}>{{ translate('pending') }}</option>
                                                    <option value="shipped" {{ $shipment->status == 'shipped' ? 'selected' : '' }}>{{ translate('shipped') }}</option>
                                                    <option value="in_transit" {{ $shipment->status == 'in_transit' ? 'selected' : '' }}>{{ translate('in_transit') }}</option>
                                                    <option value="delivered" {{ $shipment->status == 'delivered' ? 'selected' : '' }}>{{ translate('delivered') }}</option>
                                                    <option value="failed" {{ $shipment->status == 'failed' ? 'selected' : '' }}>{{ translate('failed') }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ translate('notes') }}</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ $shipment->notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('cancel') }}</button>
                                            <button type="submit" class="btn btn-primary">{{ translate('save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-2">{{ translate('no_shipments') }}</p>
                    @endforelse

                    @if($shippingCompanies->isNotEmpty())
                    <form action="{{ route('admin.orders.add-shipment', $order->id) }}" method="post" class="mt-3 pt-3 border-top">
                        @csrf
                        <h6 class="mb-2">{{ translate('add') }} {{ translate('shipment') }}</h6>
                        <div class="form-group">
                            <label>{{ translate('shipping_company') }}</label>
                            <select name="shipping_company_id" class="form-control" required>
                                <option value="">{{ translate('select') }}</option>
                                @foreach($shippingCompanies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ translate('tracking_number') }}</label>
                            <input type="text" name="tracking_number" class="form-control" placeholder="{{ translate('optional') }}">
                        </div>
                        <input type="hidden" name="status" value="pending">
                        <button type="submit" class="btn btn-primary btn-sm">{{ translate('add') }}</button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>

        @if($order->user_id != null || $order->is_guest == 1)
        <div class="col-lg-4">
            @php($wpRawPhone = $order->customer?->phone ?? ($order->delivery_address['contact_person_number'] ?? null))
            @php($wpDigits  = $wpRawPhone ? preg_replace('/[^\d]/', '', $wpRawPhone) : '')
            @php($wpNormPhone = $wpDigits ? (str_starts_with($wpDigits,'00972') ? '+972'.substr($wpDigits,5) : (str_starts_with($wpDigits,'972') ? '+972'.substr($wpDigits,3) : (str_starts_with($wpDigits,'00970') ? '+972'.substr($wpDigits,5) : (str_starts_with($wpDigits,'970') ? '+972'.substr($wpDigits,3) : (str_starts_with($wpDigits,'0')&&strlen($wpDigits)>=9 ? '+972'.substr($wpDigits,1) : (strlen($wpDigits)===9&&str_starts_with($wpDigits,'5') ? '+972'.$wpDigits : '+'.$wpDigits)))))) : null)
            @if($order['order_type'] != 'pos')
            <div class="card mb-3">
                <h4 class="mb-0 py-3 px-2 border-bottom text-center">
                    {{ $order['order_type'] != 'pos' ? translate('Order & Shipping Info ') : translate('Order Info') }}
                </h4>
                <div class="card-body text-capitalize d-flex flex-column">

                    <div class="mt-2">
                        @if($order['order_type'] != 'pos')
                        <h6 class="d-flex align-items-center gap-1">
                            {{translate('Order Status')}}
                            <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top" title="{{ translate('help_order_details_status') }}"></i>
                        </h6>
                        <select name="order_status"
                            onchange="route_alert('{{route('admin.orders.status',['id'=>$order['id']])}}'+'&order_status='+ this.value,'{{translate("Change the order status to ") }}'+  this.value.replace(/_/g, ' '))"
                            class="form-control">
                            <option value="pending" {{$order['order_status'] == 'pending'? 'selected' : ''}}>
                                {{translate('pending')}}</option>
                            <option value="confirmed" {{$order['order_status'] == 'confirmed'? 'selected' : ''}}>
                                {{translate('confirmed')}}</option>
                            <option value="processing" {{$order['order_status'] == 'processing'? 'selected' : ''}}>
                                {{translate('processing')}}</option>
                            <option value="out_for_delivery"
                                {{$order['order_status'] == 'out_for_delivery'? 'selected' : ''}}>
                                {{translate('Out_For_Delivery')}} </option>
                            <option value="delivered" {{$order['order_status'] == 'delivered'? 'selected' : ''}}>
                                {{translate('Delivered')}} </option>
                            <option value="returned" {{$order['order_status'] == 'returned'? 'selected' : ''}}>
                                {{translate('Returned')}}</option>
                            <option value="failed" {{$order['order_status'] == 'failed'? 'selected' : ''}}>
                                {{translate('Failed')}} </option>
                            <option value="canceled" {{$order['order_status'] == 'canceled'? 'selected' : ''}}>
                                {{translate('canceled')}} </option>
                        </select>

                        {{-- ── صندوق معلومات واتساب ── --}}
                        @if($wpNormPhone)
                        @php($wpAlt970 = '+970' . substr($wpNormPhone, 4))
                        <div class="mt-2 rounded px-3 py-2 d-flex align-items-start gap-2"
                             style="background:#e8f9f0;border:1px solid #25d366;font-size:0.82rem">
                            <span style="font-size:1.2rem;line-height:1.3">💬</span>
                            <div class="w-100">
                                <div class="font-weight-bold text-dark mb-1" style="font-size:0.83rem">
                                    إشعار واتساب سيُرسل إلى:
                                </div>
                                <ul class="mb-1 pr-3" style="font-size:0.83rem;line-height:1.8;font-family:monospace">
                                    <li>
                                        <span class="badge px-2 py-1"
                                              style="background:#25d366;color:#fff;letter-spacing:0.3px">
                                            {{ $wpNormPhone }}
                                        </span>
                                        <span class="text-muted mr-1" style="font-family:sans-serif;font-size:0.75rem">
                                            (واتساب ✓)
                                        </span>
                                    </li>
                                    <li class="text-muted">
                                        {{ $wpAlt970 }}
                                        <span style="font-family:sans-serif;font-size:0.75rem">
                                            (السلطة الفلسطينية)
                                        </span>
                                    </li>
                                </ul>
                                @if($wpRawPhone && $wpRawPhone !== $wpNormPhone)
                                <div class="text-muted" style="font-family:sans-serif;font-size:0.76rem">
                                    الرقم الأصلي: {{ $wpRawPhone }}
                                </div>
                                @endif
                                <div class="mt-1" style="color:#b85c00;font-size:0.78rem;font-family:sans-serif">
                                    ⚠️ تأكد أن الرقم <strong>{{ $wpNormPhone }}</strong> مربوط بواتساب وإلا لن يصل الإشعار
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="mt-2 rounded px-3 py-2"
                             style="background:#fff8e1;border:1px solid #ffc107;font-size:0.82rem;color:#856404">
                            ⚠️ لا يوجد رقم هاتف للعميل — <strong>لن يُرسل إشعار واتساب</strong>
                        </div>
                        @endif

                        @endif
                    </div>

                    <div class="mt-3">
                        @if($order['order_type'] != 'pos')
                        <h6 class="d-flex align-items-center gap-1">
                            {{translate('Payment Status')}}
                            <i class="tio-info-outlined text-muted fs-14" data-toggle="tooltip" data-placement="top" title="{{ translate('help_order_details_payment') }}"></i>
                        </h6>
                        <select name="order_status"
                            onchange="route_alert('{{route('admin.orders.payment-status',['id'=>$order['id']])}}'+'&payment_status='+ this.value,'{{translate("Change status to ")}}' + this.value)"
                            class="form-control">
                            <option value="paid" {{$order['payment_status'] == 'paid'? 'selected' : ''}}>
                                {{translate('paid')}}</option>
                            <option value="unpaid" {{$order['payment_status'] == 'unpaid'? 'selected' : ''}}>
                                {{translate('unpaid')}} </option>
                        </select>
                        @endif
                    </div>

                    {{-- ── Custom / Dynamic Status Update ─────────────────────── --}}
                    <div class="card mt-3 border-left border-primary" style="border-left:3px solid #10b46a !important">
                        <div class="card-header py-2">
                            <h6 class="card-header-title mb-0 d-flex align-items-center gap-1">
                                <i class="tio tio-edit" style="color:#10b46a"></i>
                                إضافة تحديث للطلب
                                <small class="text-muted font-weight-normal mr-2">— نص حر، يُرسل للعميل عبر واتساب</small>
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <form action="{{ route('admin.orders.add-update', $order->id) }}" method="POST">
                                @csrf
                                <div class="form-group mb-2">
                                    <label class="form-control-label small font-weight-bold">الحالة / المرحلة <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="custom_status"
                                           class="form-control form-control-sm"
                                           placeholder="مثال: قيد الطباعة، جاهز للاستلام، تم الشحن عبر DHL…"
                                           required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-control-label small font-weight-bold">ملاحظة (اختياري)</label>
                                    <textarea name="note"
                                              class="form-control form-control-sm"
                                              rows="2"
                                              placeholder="تفاصيل إضافية للعميل…"></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="tio tio-send"></i> حفظ وإشعار العميل
                                </button>
                            </form>
                        </div>
                    </div>

                    @include('admin-views.order.partials._status-log')

                </div>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-header-title"><i class="tio tio-user"></i> {{translate('Customer_Information')}}
                    </h4>
                </div>

                <div class="card-body">
                    <div class="media gap-3">
                        @if($order->is_guest == 1)
                        <div class="media-body d-flex flex-column gap-1 text-dark">
                            <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                {{translate('Guest Customer')}}
                            </span>
                        </div>
                        @else
                        @if($order->customer)
                        <div class="avatar-lg rounded-circle">
                            <img class="img-fit rounded-circle" src="{{$order->customer->image_fullpath}}"
                                alt="Image Description">
                        </div>
                        <div class="media-body d-flex flex-column gap-1 text-dark">
                            <div>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</div>
                            <div>{{\App\Models\Order::where('user_id',$order['user_id'])->count()}}
                                {{translate('orders')}}</div>
                            <a class="text-dark"
                                href="tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                            <a class="text-dark"
                                href="mailto:{{$order->customer['email']}}">{{$order->customer['email']}}</a>
                        </div>
                        @else
                        <div class="media-body d-flex flex-column gap-1 text-dark">
                            <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                {{translate('Customer_deleted')}}
                            </span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
            @if($order['order_type']!='self_pickup' && $order['order_type'] != 'pos')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">
                        <i class="tio tio-user"></i>
                        {{translate('Delivery_Address')}}
                    </h4>
                </div>

                <div class="card-body">
                    @php($address = $order->deliveryAddress ?? \App\Models\CustomerAddress::find($order['delivery_address_id']))
                    <div class="d-flex justify-content-between gap-3">
                        @if($address)
                        <div class="delivery--information-single flex-column flex-grow-1">
                            <div class="d-flex">
                                <div class="name">{{translate('name')}}</div>
                                <div class="info">{{$address['contact_person_name']}}</div>
                            </div>
                            <div class="d-flex">
                                <div class="name">{{translate('contact')}}</div>
                                <a href="tel:{{$address['contact_person_number']}}"
                                    class="info">{{$address['contact_person_number']}}</a>
                            </div>
                            @if(!empty($address['city']))
                            <div class="d-flex">
                                <div class="name">{{translate('city')}}</div>
                                <div class="info">{{$address['city']}}</div>
                            </div>
                            @endif
                            @if($address['floor'])
                            <div class="d-flex">
                                <div class="name">{{translate('floor')}}</div>
                                <div class="info">#{{$address['floor']}}</div>
                            </div>
                            @endif
                            @if($address['house'])
                            <div class="d-flex">
                                <div class="name">{{translate('house')}}</div>
                                <div class="info">#{{$address['house']}}</div>
                            </div>
                            @endif
                            @if($address['road'])
                            <div class="d-flex">
                                <div class="name">{{translate('road')}}</div>
                                <div class="info">#{{$address['road'] }}</div>
                            </div>
                            @endif
                            @if($googleMapStatus == 1 && isset($address['latitude']) && isset($address['longitude']))
                            <hr class="w-100">
                            <div>
                                <a target="_blank" class="text-dark d-flex align-items-center gap-3"
                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                    <i class="tio-map"></i> {{$address['address']}}<br>
                                </a>
                            </div>
                            @else
                            <div class="d-flex">
                                <div class="name">{{translate('address')}}</div>
                                <div class="info">#{{$address['address'] }}</div>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-muted">{{ translate('delivery_address_not_set') }}</div>
                        @endif
                        @if($address)
                        <a class="link" data-toggle="modal" data-target="#shipping-address-modal" href="javascript:"><i
                                class="tio tio-edit"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="mySmallModalLabel">{{translate('reference')}} {{translate('code')}}
                    {{translate('add')}}</h5>
                <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                    aria-label="Close">
                    <i class="tio-clear tio-lg"></i>
                </button>
            </div>

            <form action="{{route('admin.orders.add-payment-ref-code',[$order['id']])}}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="transaction_reference" class="form-control" placeholder="EX : Code123"
                            required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div id="shipping-address-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalTopCoverTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-top-cover bg-dark text-center">
                <figure class="position-absolute right-0 bottom-0 left-0 mb-minus-1px">
                    <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                        viewBox="0 0 1920 100.1">
                        <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z" />
                    </svg>
                </figure>

                <div class="modal-close">
                    <button type="button" class="btn btn-icon btn-sm btn-ghost-light" data-dismiss="modal"
                        aria-label="Close">
                        <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor"
                                d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="modal-top-cover-icon">
                <span class="icon icon-lg icon-light icon-circle icon-centered shadow-soft">
                    <i class="tio-location-search"></i>
                </span>
            </div>

            @php($address=\App\Models\CustomerAddress::find($order['delivery_address_id']))
            @php($cities = \App\Models\City::orderBy('sort_order')->orderBy('name')->pluck('name')->toArray())
            @if(isset($address))
            <form action="{{route('admin.orders.update-shipping',[$order['delivery_address_id']])}}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('name')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <input type="text" class="form-control" name="contact_person_name"
                                value="{{$address['contact_person_name']}}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('contact')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <input type="text" class="form-control" name="contact_person_number"
                                value="{{$address['contact_person_number']}}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('city')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <select class="form-control" name="city" required>
                                <option value="">{{ translate('select') }} {{ translate('city') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ ($address['city'] ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('address')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <input type="text" class="form-control" name="address" value="{{$address['address']}}"
                                required>
                        </div>
                    </div>

                    @if($googleMapStatus)
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('latitude')}}
                        </label>
                        <div class="col-md-4 js-form-message">
                            <input type="text" class="form-control" name="latitude" value="{{$address['latitude']}}">
                        </div>
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('longitude')}}
                        </label>
                        <div class="col-md-4 js-form-message">
                            <input type="text" class="form-control" name="longitude" value="{{$address['longitude']}}">
                        </div>
                    </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">{{translate('close')}}</button>
                    <button type="submit" class="btn btn-primary">{{translate('save')}}
                        {{translate('changes')}}</button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

<!--- Edit Warning --->
<div class="modal fade" id="edit-product-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mxwidth">
        <div class="modal-content shadow-sm pb-sm-3">
            <div class="modal-header p-0">
                <button type="button"
                    class="close w-35 h-35 rounded-circle d-flex align-items-center justify-content-center bg-light position-relative"
                    data-dismiss="modal" aria-label="Close" style="top: 10px; inset-inline-end: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('assets/admin/img/delete-warning.png')}}" alt="" class="mb-3">
                <h3 class="mb-2">{{ translate('Are you sure') }}?</h3>
                <p class="m-0">{{ translate('You want to edit this order') }}?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 gap-2">
                <button type="button" class="btn min-w-120 btn--reset" data-dismiss="modal">{{ translate('No') }}</button>
                <button type="button" class="btn min-w-120 btn-primary">{{ translate('Yes') }}</button>
            </div>
        </div>
    </div>
</div>
<!--- Delete Warning --->
<div class="modal fade" id="delete-product-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mxwidth">
        <div class="modal-content shadow-sm pb-sm-3">
            <div class="modal-header p-0">
                <button type="button"
                    class="close w-35 h-35 rounded-circle d-flex align-items-center justify-content-center bg-light position-relative"
                    data-dismiss="modal" aria-label="Close" style="top: 10px; inset-inline-end: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('assets/admin/img/delete-warning.png')}}" alt="" class="mb-3">
                <h3 class="mb-2">{{ translate('Are you sure to delete this Product') }}?</h3>
                <p class="m-0">{{ translate('If once you delete this product, this will remove from product list.') }} </p>
            </div>
            <div class="modal-footer justify-content-center border-0 gap-2">
                <button type="button" class="btn min-w-120 btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <button type="button" class="btn min-w-120 btn-danger">{{ translate('Delete') }}</button>
            </div>
        </div>
    </div>
</div>

@if($order->type != 'pos' && in_array($order->order_status, ['pending', 'confirmed', 'processing']) && $order->payment_status == 'unpaid')
    <div id="offcanvas__order_edit" class="custom-offcanvas d-flex flex-column justify-content-between"
         style="--offcanvas-width: 750px">
        <div>
            <span class="data-to-js"
                  data-order-id="{{ $order['id'] }}"
                  data-product-details="{{ json_encode($orderedProducts) }}"
                  data-max-limit-message="{{ translate('maximum stock limit reached') }}"
                  data-min-limit-message="{{ translate('minimum 1 quantity is required') }}"
                  data-search-product-route="{{ route('admin.orders.search-product') }}"
                  data-default-image="{{ asset('assets/admin/img/160x160/img2.jpg') }}"
                  data-currency-symbol="{{ Helpers::currency_symbol() }}"
                  data-currency-symbol-position="{{ Helpers::get_business_settings('currency_symbol_position') }}"
            >

            </span>
            <div class="custom-offcanvas-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <div class="bg-white px-3 py-3 d-flex justify-content-between w-100">
                    <div>
                        <h2 class="mb-1">{{ translate('Edit Order') }}</h2>
                        <div class="d-flex flex-wrap align-items-center gapy_30px">
                            <h3 class="page-header-title d-flex align-items-center gap-2">
                                <span class="font--max-sm fs-14">{{ translate('Order') }} #{{ $order['id'] }}</span>
                                <span class="badge badge-soft-info font-regular m-0">{{ translate($order->order_status) }}</span>
                            </h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-14 font-regular d-block text-dark">{{ translate('Order Placed') }} :</span>
                                <span class="fs-14 font-weight-bolder d-block text-dark"> {{ date('d M Y h:i a', strtotime($order->created_at)) }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            class="btn-close w-35 h-35 min-w-35 rounded-circle d-flex align-items-center justify-content-center bg--secondary position-relative offcanvas-close border-0 fs-18"
                            aria-label="Close">&times;
                    </button>
                </div>
            </div>
            <div class="custom-offcanvas-body p-20">
                <div class="mb-20 position-relative edit-search-form">
                    <div class="form-control bg-white d-flex align-items-center gap-2">
                        <i class="tio-search"></i>
                        <input type="text" name="search" class="h-100 fs-12 bg-transparent w-100 border-0 rounded-0"
                               value="" placeholder="{{ translate('Search by name and id, press enter to add') }}" autocomplete="off">
                        <!--- After Search -->
                        <div class="search-wrap-manage w-100 d-none">
                            <div class="search-items-wrap p-sm-3 p-2 rounded bg-white d-flex flex-column gap-2">
                               @include('admin-views.order.partials.product-search-result')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3 align-items-center mb-10px">
                    <h6 class="m-0">{{ translate('Products List') }} </h6>
                    <span class="badge badge-soft-dark rounded-50 fz-10 count-total-products">{{ $order->details->count() }}</span>
                </div>
                <div class="table-responsive pt-0 card mb-20">
                    <table
                        class="table table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer mb-0" id="productListToUpdate">
                        <thead class="border-0 table-th-bg p-0">
                        <tr>
                            <th class="border-0">{{ translate('Sl') }}</th>
                            <th class="border-0">{{ translate('Item Description') }}</th>
                            <th class="border-0 text-center">{{ translate('Qty') }}</th>
                            <th class="border-0 text-right">{{ translate('Total') }}</th>
                            <th class="border-0">{{ translate('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="offcanvas-footer w-100 bg-white p-3 d-flex align-items-center justify-content-end gap-3">
                <button type="button" class="btn min-w-120 btn--secondary h--40px reset offcanvas-close">{{ translate('Cancel') }}</button>
                <button type="submit" class="btn min-w-120 btn-primary h--40px update-product-list">{{ translate('Update Cart ') }}</button>
            </div>
        </div>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
    <div class="modal cmn__quick-modal fade" id="quick-view" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="--modal-mxwidth: 650px; max-width: 650px !important;">
            <div class="modal-content" id="quick-view-modal">

            </div>
        </div>
    </div>
@endif
@include('admin-views.modal.print-invoice-modal')
@endsection
@push('script_2')
    <script type="text/javascript" src="{{asset('assets/admin/js/offcanvas.js')}}"></script>

    <script>
        "use strict"

        $('input[name="search"]').on('keyup', function() {
    let value = $(this).val().toLowerCase();
    if (value.length > 0) {
        $('.search-wrap-manage').removeClass('d-none');
        $.ajax({
            url: $('.data-to-js').data('search-product-route'),
            method: 'GET',
            data: {
                'search': value,
                'order_id': $('.data-to-js').data('order-id'),
            },
            success: function (data) {
                $('.search-items-wrap').empty().html(data.view);
            }
        })
    } else {
        $('.search-wrap-manage').addClass('d-none');
    }
});

function renderProductDetailsToUpdate() {
    var $dataToJs = $('.data-to-js');
    var products = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    var $tbody = $('#productListToUpdate tbody');
    $tbody.empty();
    $.each(products, function (key, product) {
        var rowClass = (product.total_stock == product.quantity) ? 'max-limit' : '';
        var img = product.image ? product.image : $('.data-to-js').data('default-image');
        var variantHtml = product.variant ? `<div class="d-flex align-items-center gap-1 fs-12">{{ translate('variation') }} : <span class="text-dark">${product.variant}</span></div>` : '';
        var row = `
            <tr class="custom__tr ${rowClass} ${product.newly_added ? 'active' : '' } " data-id="${key}">
                <td>${key + 1}</td>
                <td>
                    <div class="list-items-media cursor-pointer d-flex align-items-center gap-2 quick-View" data-id="${product.id || ''}">
                        <img width="50" height="50" src="${img}" class="rounded" alt="image">
                        <div class="cont d-flex align-justify-content-center flex-column gap-0">
                            <p class="fs-12 text-dark mb-1 max-w-220 line--limit-1">${product.name}</p>
                            <div class="d-flex align-items-center gap-1 fs-12">
                                {{ translate('Unit Price') }} : <span class="text-dark">${product.price_with_symbol}</span>
                            </div>
                            ${variantHtml}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="product-quantity min-w-100 mx-auto">
                        <div class="input-group bg-white rounded border d-flex justify-content-center align-items-center">
                            <span class="input-group-btn w-30px">
                                <button class="btn px-2 btn-number bg-transparent w-30px" type="button" data-type="minus">
                                    <i class="tio-remove font-weight-bold"></i>
                                </button>
                            </span>
                            <input type="text"
                                   class="w-25px input-number form-control p-0 border-0 text-center text-dark"
                                   value="${product.quantity}"
                                   placeholder="${product.quantity}" min="1" data-maximum_quantity="${product.total_stock}" data-base-price="${product.base_price}" data-discount-price="${product.product_discount}">
                            <span class="input-group-btn w-30px">
                                <button class="btn px-2 btn-number bg-transparent w-30px" type="button" data-type="plus">
                                    <i class="tio-add font-weight-bold"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </td>
                <td class="fs-14 text-dark text-right">
                <span class="product-total-price fs-12 text-decoration-line-through ${product.discount == 0 ? 'd-none' : ''}">
                ${product.total_price}
                </span>
                ${product.discount == 0 ? '' : '<br>'}
                <span class="product-total-discount-price">${ product.discount == 0 ? product.total_price : product.total_discount_price }</span>
                </td>
                <td class="text-center">
                    <a class="btn btn-danger rounded-circle square-btn" href="javascript:" data-toggle="modal" data-target="#delete-product-modal">
                        <i class="tio tio-delete"></i>
                    </a>
                </td>
            </tr>
        `;
        $tbody.append(row);
    });
    manageQuantity();
    $('.count-total-products').text(products.length)
}
renderProductDetailsToUpdate()

$(document).on('click', '.add-searched-product', function(){
    let $dataToJs = $('.data-to-js');
    let existedProducts = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    let exist = existedProducts.some(product => product.id == $(this).data('id') && product.variant == $(this).data('variant'));
    if (exist) {
        toastr.error('{{ translate("Product already added!") }}', {
            CloseButton: true,
            ProgressBar: true
        });
    } else {
        let newProduct = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            quantity: 1,
            variant: $(this).data('variant'),
            price_with_symbol: $(this).data('price'),
            product_discount: $(this).data('discount-price'),
            discount: $(this).data('discount'),
            image: $(this).data('image'),
            total_stock: $(this).data('stock'),
            total_price: $(this).data('price'),
            base_price: $(this).data('base-price'),
            price: $(this).data('base-price'),
            total_discount_price: $(this).data('total-discount-price'),
            newly_added: true,
        }
        existedProducts.push(newProduct);
        $dataToJs.attr('data-product-details', JSON.stringify(existedProducts));
    }

    renderProductDetailsToUpdate();

    $('input[name="search"]').val('');
    $('.search-wrap-manage').addClass('d-none');
});

let deleteRowIndex = null;
$(document).on('click', '.btn-danger[data-target="#delete-product-modal"]', function() {
    const $row = $(this).closest('tr');
    deleteRowIndex = $row.data('id');
});
$('#delete-product-modal .btn-danger').on('click', function() {
    if (deleteRowIndex !== null) {
        let $dataToJs = $('.data-to-js');
        let existedProducts = JSON.parse($dataToJs.attr('data-product-details') || '[]');
        existedProducts.splice(deleteRowIndex, 1);
        $dataToJs.attr('data-product-details', JSON.stringify(existedProducts));

        renderProductDetailsToUpdate();

        deleteRowIndex = null;
        $('#delete-product-modal').modal('hide');
    }
});

$(document).off('click', '.quick-view').on('click', '.quick-view', function(){
    let productId = $(this).data('id');
    quickView(productId);
    $('input[name="search"]').val('');
    $('.search-wrap-manage').addClass('d-none');
});

function quickView(product_id) {
    let $dataToJs = $('.data-to-js');
    let productList = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    $.ajax({
        url: '{{route('admin.orders.quick-view')}}',
        type: 'GET',
        data: {
            product_id: product_id,
            product_list: productList,
        },
        dataType: 'json',
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            $('#quick-view').modal('show');
            $('#quick-view-modal').empty().html(data.view);
        },
        complete: function () {
            $('#loading').hide();
        },
    });
}

$(document).on('click', '#add-to-cart-form .btn-number', function (e) {
    e.preventDefault();
    let $btn = $(this);
    let fieldName = $btn.attr('data-field');
    let type = $btn.attr('data-type');
    let $input = $("#add-to-cart-form input[name='" + fieldName + "']");
    let currentVal = parseInt($input.val());
    let $tooltip = $('.custom-tooltip');

    if (!isNaN(currentVal)) {
        if (type === 'minus') {
            if (currentVal > $input.attr('min')) {
                $input.val(currentVal - 1).trigger('change');
                $tooltip.hide();
                $("[data-type='plus'][data-field='" + fieldName + "']").prop('disabled', false);
            }
            if (parseInt($input.val()) <= $input.attr('min')) {
                $btn.attr('disabled', true);
            }
        } else if (type === 'plus') {
            if (currentVal < parseInt($input.attr('max'))) {
                $input.val(currentVal + 1).trigger('change');
                $("[data-type='minus'][data-field='" + fieldName + "']").prop('disabled', false);
            }
            if (currentVal >= parseInt($input.attr('max'))) {
                $tooltip.css('display', 'flex');
                $btn.prop('disabled', true);
            }
        }
    } else {
        $input.val(0);
    }
});

$(document).on('focusin', '#add-to-cart-form .input-number', function () {
    $(this).data('oldValue', $(this).val());
});

$(document).on('change', '#add-to-cart-form .input-number', function () {
    let $input = $(this);
    let name = $input.attr('name');
    let minValue = parseInt($input.attr('min')) || 0;
    let maxValue = parseInt($input.attr('max')) || 100;
    let valueCurrent = parseInt($input.val());
    let $tooltip = $('.custom-tooltip');

    if (isNaN(valueCurrent)) {
        $input.val($input.data('oldValue'));
        return;
    }
    if (valueCurrent <= minValue) {
        $input.val(minValue);
        $("[data-type='minus'][data-field='" + name + "']").attr('disabled', true);
        $("[data-type='plus'][data-field='" + name + "']").removeAttr('disabled');
    } else if (valueCurrent >= maxValue) {
        $input.val(maxValue);
        $("[data-type='plus'][data-field='" + name + "']").attr('disabled', true);
        $("[data-type='minus'][data-field='" + name + "']").removeAttr('disabled');
        $tooltip.css('display', 'flex');
    } else {
        $("[data-type='minus'][data-field='" + name + "']").removeAttr('disabled');
        $("[data-type='plus'][data-field='" + name + "']").removeAttr('disabled');
        $tooltip.hide();
    }
});

$(document).on('keydown', '#add-to-cart-form .input-number', function (e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
        (e.keyCode == 65 && e.ctrlKey === true) ||
        (e.keyCode >= 35 && e.keyCode <= 39)) {
        return;
    }
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) &&
        (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});

function getVariantPrice(initial = false) {
    let $form = $('#add-to-cart-form');
    let $quantityInput = $('#quantity');
    let quantity = parseInt($quantityInput.val()) || 1;
    if (quantity <= 0 || !checkAddToCartValidity()) return;
    let $dataToJs = $('.data-to-js');
    let productList = JSON.parse($dataToJs.attr('data-product-details') || '[]');

    let formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id: $('input[name="id"]').val(),
        quantity: quantity,
        product_list: productList,
    };

    $form.find('input[type=radio]:checked').each(function () {
        formData[$(this).attr('name')] = $(this).val();
    });

    if (initial) {
        $.ajax({
            type: 'GET',
            url: '{{ route('admin.orders.quick-view-modal-footer') }}',
            data: formData,
            success: function (data) {
                $('#quick-view-modal-footer').html(data.view);
                $form.find('.total-stock').text(data.stock);
            },
            error: function (xhr) {
                console.error(xhr.responseJSON || xhr.responseText);
            }
        });
    } else {
        $.ajax({
            type: "POST",
            url: '{{ route('admin.orders.variant_price') }}',
            data: formData,
            success: function (data) {
                $('#chosen_price_div').removeClass('d-none');
                $('#chosen_price').html(round(data.price, 2).toFixed(2));
                $(".total-stock").html(data.stock);
                $quantityInput.attr("max", data.stock);
                if (parseInt($quantityInput.val()) > data.stock) {
                    $quantityInput.val(data.stock).trigger('change');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseJSON || xhr.responseText);
            }
        });
    }
}

function addToCart(form_id = 'add-to-cart-form') {
    if (!checkAddToCartValidity()) {
        Swal.fire({
            type: 'info',
            title: '{{translate('Edit Order')}}',
            confirmButtonText: '{{translate("Ok")}}',
            text: '{{translate('Please choose all the options')}}'
        });
        return;
    }
    let $dataToJs = $('.data-to-js');
    let existedProducts = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    let currencySymbol = $dataToJs.data('currency-symbol');
    let currencySymbolPosition = $dataToJs.data('currency-symbol-position');
    let formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id: $('input[name="id"]').val(),
        quantity: $('input[name="quantity"]').val(),
        product_list: existedProducts,
    };
    $('[type=radio]:checked').each(function () {
        formData[$(this).attr('name')] = $(this).val();
    });

    $.post({
        url: '{{ route('admin.orders.add-to-cart') }}',
        data: formData,
        beforeSend: function () { $('#loading').show(); },
        success: function (data) {
            let productData = data.data;
            let existingIndex = existedProducts.findIndex(p => p.id == productData.id && p.variant == productData.variant);

            if (existingIndex !== -1) {
                existedProducts[existingIndex].quantity = parseInt(productData.quantity);
                existedProducts[existingIndex].total_price = currencySymbolPosition == 'left'
                    ? currencySymbol + (existedProducts[existingIndex].base_price * existedProducts[existingIndex].quantity).toFixed(2)
                    : (existedProducts[existingIndex].base_price * existedProducts[existingIndex].quantity).toFixed(2) + currencySymbol;
            } else {
                let newProduct = {
                    id: productData.id,
                    name: productData.name,
                    quantity: parseInt(productData.quantity),
                    variant: productData.variant,
                    price_with_symbol: currencySymbolPosition == 'left'
                        ? currencySymbol + productData.price.toFixed(2)
                        : productData.price.toFixed(2) + currencySymbol,
                    image: productData.image?.[0] ?? null,
                    total_stock: productData.total_stock,
                    total_price: currencySymbolPosition == 'left'
                        ? currencySymbol + (productData.price * productData.quantity).toFixed(2)
                        : (productData.price * productData.quantity).toFixed(2) + currencySymbol,
                    base_price: productData.price,
                    price: productData.price,
                    product_discount: productData.price - productData.discount,
                    discount: productData.discount,
                    total_discount_price: currencySymbolPosition == 'left'
                        ? currencySymbol + ((productData.price - productData.discount) * productData.quantity).toFixed(2)
                        : ((productData.price - productData.discount) * productData.quantity).toFixed(2) + currencySymbol,
                    newly_added: true,
                };
                existedProducts.push(newProduct);
            }

            $dataToJs.attr('data-product-details', JSON.stringify(existedProducts));

            renderProductDetailsToUpdate();
            $('.call-when-done').click();

            toastr.success('{{translate('Item has been added to the list')}}!', {
                CloseButton: true,
                ProgressBar: true
            });
        },
        complete: function () { $('#loading').hide(); }
    });
}

function checkAddToCartValidity() {
    return true;
}

function updateProductList() {
    let $dataToJs = $('.data-to-js');
    let orderId = $dataToJs.data('order-id');
    let products = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    $.ajax({
        type: "POST",
        url: '{{ route('admin.orders.update-product-list', ':id') }}'.replace(':id', orderId),
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            products: products,
        },
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            if (data.status == true) {
                toastr.success('{{translate("Product list updated successfully")}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                $('.update-product-list').attr('disabled', true);
                setTimeout(function () {
                    location.reload();
                }, 1200);
            }
        },
        error: function () {
            toastr.error('{{translate("Something went wrong")}}', {
                CloseButton: true,
                ProgressBar: true
            });
            $('.update-product-list').attr('disabled', false);
        },
        complete: function () {
            $('#loading').hide();
        }
    });
}

$('.update-product-list').on('click', function() {
    updateProductList();
});
</script>
@endpush
