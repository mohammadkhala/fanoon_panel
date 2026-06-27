<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ translate('Invoice') }}</title>
    <link rel="stylesheet" href="{{asset('assets/admin/css/font/open-sans.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.css')}}">

    <style>
        body, main, .content { direction: rtl; }
        .table th, .table td { text-align: right; }
        .dl dd { text-align: left; }
        @media print {
          .badge-soft-info {
            background-color: #eff4ff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color-adjust: exact;
          }
          body, main { direction: rtl; }
        }
    </style>

</head>

<body class="footer-offset">
<main id="content" role="main" class="main pointer-event">
    <div class="content container-fluid">
        <div class="row">
            @php($logo = Helpers::get_business_settings('logo'))
            @php($companyName = Helpers::get_business_settings('store_name') ?? Helpers::get_business_settings('restaurant_name'))
            <div class="col-12 text-center mb-3">
                <img width="150"
                     src="{{Helpers::onErrorImage(
                            $logo,
                            asset('storage/ecommerce').'/' . $logo,
                            asset('assets/admin/img/160x160/img2.jpg') ,
                            'ecommerce/')}}"
                     alt="{{  translate('logo') }}">
                @if($companyName)
                <h4 class="mb-1 mt-2">{{ $companyName }}</h4>
                @endif
                <h3 class="mb-5 mt-2">{{ translate('Invoice') }} : #{{$order['id']}}</h3>
            </div>
            <div class="col-6 text-dark">
                @if($order->is_guest == 0)
                    @if($order->customer)
                        <h3>{{ translate('Customer Info') }}</h3>
                        <div>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</div>
                        <div>{{$order->customer['email']}}</div>
                        <div>{{$order->customer['phone']}}</div>
                        @if($order->delivery_address)
                            @if(!empty($order->delivery_address['city']))<div>{{$order->delivery_address['city']}}</div>@endif
                            <div>{{$order->delivery_address['address']}}</div>
                        @endif
                        <br>
                    @endif
                @else
                    @php($address=\App\Models\CustomerAddress::find($order['delivery_address_id']))
                    @if(isset($address))
                        <h3>{{ translate('Customer Info') }}</h3>
                        <div>{{$address['contact_person_name']}}</div>
                        <div>{{$address['contact_person_number']}}</div>
                        @if(!empty($address['city']))<div>{{$address['city']}}</div>@endif
                        <div>{{$address['address']}}</div><br>
                    @endif
                @endif
            </div>

            <div class="col-6 text-dark text-end">
                <h3>{{ translate('Billing Address') }}</h3>
                <div>{{Helpers::get_business_settings('phone')}}</div>
                <div>{{Helpers::get_business_settings('email_address')}}</div>
                <div>{{Helpers::get_business_settings('address')}}</div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @php($item_amount=0)
                @php($sub_total=0)
                @php($total_tax=0)
                @php($total_dis_on_pro=0)
                @php($total_item_discount=0)

                <div class="table-responsive">
                    <table class="table table-bordered table-align-middle text-dark">
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
                            @php($pd = $detail->product_details ?? null)
                            @php($productDetails = is_array($pd) ? $pd : (is_string($pd) ? json_decode($pd, true) : (is_object($pd) ? (array)$pd : null)))
                            @php($vr = $detail->variation ?? null)
                            @php($varRaw = is_array($vr) ? $vr : (is_string($vr) ? json_decode($vr, true) : (is_object($vr) ? (array)$vr : [])))
                            @php($op = $orderedProducts[$index] ?? null)
                            @php($productName = (is_array($productDetails) && isset($productDetails['name'])) ? $productDetails['name'] : (($op ?? [])['name'] ?? translate('Product deleted')))
                            @php($imgFull = ($detail->product && $detail->product->image) ? ($detail->product->image_fullpath ?? []) : [])
                            @php($productImage = (is_array($imgFull) && !empty($imgFull)) ? $imgFull[0] : (($op ?? [])['image'] ?? asset('assets/admin/img/160x160/img2.jpg')))
                            @php($varArr = is_array($varRaw) ? $varRaw : [])
                            @php($varShow = !empty($varArr) ? ($varArr[0] ?? $varArr) : [])
                            @php($varShow = is_array($varShow) ? $varShow : [])
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="media gap-3 max-content">
                                            <div class="avatar-xl">
                                                <img class="img-fit" src="{{ $productImage }}" alt="{{ translate('image') }}">
                                            </div>
                                            <div class="media-body">
                                                <h6 class="mb-1 w-24ch">{{ $productName }}</h6>
                                                @if(!empty($varShow))
                                                    <h6 class="underline mb-0">{{ translate('variation') }}:</h6>
                                                    @foreach($varShow as $key1 => $variation)
                                                        <div class="fs-12">{{ $key1 }}: {{ $variation }}</div>
                                                    @endforeach
                                                @elseif($op && trim((string)(($op ?? [])['variant'] ?? '')) !== '')
                                                    <div class="fs-12">{{ translate('variation') }}: {{ ($op ?? [])['variant'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Helpers::set_symbol($detail['price']) }}</td>
                                    <td>{{ Helpers::set_symbol($detail['discount_on_product']) }}</td>
                                    <td>{{ $detail['quantity'] }}</td>
                                    <td class="text-end">
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

                <div class="row justify-content-md-end g-2">
                    <div class="col-sm-6">
                        @if($order['bring_change_amount'] > 0)
                            <div class="badge badge-soft-info p-2 d-flex align-items-center gap-1 text-wrap text-left lh-1.3 border-0">
                                <i class="tio-info"></i>
                                <span class="text-dark opacity-lg">
                                {{translate('Please_bring').' '. \App\CentralLogics\Helpers::set_symbol($order['bring_change_amount']) . ' '.  translate('in_change_for_the_customer_when_making_the_delivery')}}
                            </span>
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <dl class="row">
                            <dt class="col-sm-6">{{ translate('Items Price') }}:</dt>
                            <dd class="col-sm-6 text-right">{{ Helpers::set_symbol($item_amount) }}</dd>

                            <dt class="col-sm-6">{{ translate('item_discount') }}:</dt>
                            <dd class="col-sm-6 text-right">{{ Helpers::set_symbol($total_item_discount) }}</dd>

                            <dt class="col-sm-6">{{ translate('Subtotal') }}:</dt>
                            <dd class="col-sm-6 text-right">{{ Helpers::set_symbol($sub_total) }}</dd>

                            <dt class="col-sm-6">{{ translate('Coupon Discount') }}:</dt>
                            <dd class="col-sm-6 text-right">
                                - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>

                            @if(($order['loyalty_points_used'] ?? 0) > 0)
                            <dt class="col-sm-6">{{ translate('loyalty_discount') }} ({{ $order['loyalty_points_used'] }} {{ translate('points') }}):</dt>
                            <dd class="col-sm-6 text-right">
                                - {{ Helpers::set_symbol($order['loyalty_discount_amount'] ?? 0) }}</dd>
                            @endif

                            <dt class="col-6">{{translate('Extra Discount')}}:</dt>
                            <dd class="col-6 text-right">
                                - {{ Helpers::set_symbol($order['extra_discount']) }}</dd>

                            <dt class="col-sm-6">{{ translate('Delivery Fee') }}:</dt>
                            <dd class="col-sm-6 text-right">
                                {{ Helpers::set_symbol($deliveryChargeDisplay ?? $order['delivery_charge']) }}
                            </dd>

                            <dt class="col-sm-6 border-top font-weight-bold pt-2">{{ translate('Total') }}:</dt>
                            <dd class="col-sm-6 border-top font-weight-bold text-right pt-2">{{ Helpers::set_symbol($order['order_amount']) }}</dd>

                            @if($order['order_type'] == 'pos' && $order['paid_amount'] >0)
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

        <div class="text-dark mt-10">
            @php($whatsappRaw = Helpers::get_business_settings('whatsapp'))
            @php($whatsappData = is_array($whatsappRaw) ? $whatsappRaw : (is_string($whatsappRaw) ? json_decode($whatsappRaw, true) : []))
            @php($whatsappNumber = (is_array($whatsappData) && !empty($whatsappData['number'])) ? $whatsappData['number'] : Helpers::get_business_settings('phone'))
            @php($whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber))
            <div class="text-center mb-3">{{ translate('If you require any assistance or have feedback or suggestions about our site you can') }}
                 <br /> {{ translate('contact us on WhatsApp') }}: <a href="https://wa.me/{{ $whatsappNumber }}" class="text-primary" target="_blank">{{ $whatsappNumber ? '+'.$whatsappNumber : Helpers::get_business_settings('phone') }}</a>
            </div>

            <div class="invoice-footer-bg py-5 px-4">
                <div class="text-center">
                    <div>{{ translate('phone') }}: {{ Helpers::get_business_settings('phone') }}</div>
                    <div>{{ translate('WhatsApp') }}: {{ $whatsappNumber ? '+'.$whatsappNumber : Helpers::get_business_settings('phone') }}</div>
                    <div><?php echo url('/') ?></div>
                    <div>
                        <a href="https://baitpait.com" target="_blank" class="text-dark text-decoration-none">تطوير وبرمجة بيت البرمجيات وتكنولوجيا المعلومات</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="{{asset('assets/admin/js/jquery.js')}}"></script>
<script src="{{asset('assets/admin/js/demo.js')}}"></script>
<script src="{{asset('assets/admin/js/vendor.min.js')}}"></script>
<script src="{{asset('assets/admin/js/bootstrap.js')}}"></script>
<script src="{{asset('assets/admin/js/theme.min.js')}}"></script>
<script>
    window.print();
</script>
</body>
</html>
