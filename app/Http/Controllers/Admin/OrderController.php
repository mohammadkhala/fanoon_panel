<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Services\WaSenderService;
use App\Traits\OrderPricing;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    use OrderPricing;

    public function __construct(
        private Order       $order,
        private OrderDetail $orderDetail,
        private Product     $product,
        private Branch      $branch
    )
    {
    }

    /**
     * صفحة توجيه: الطلبات تُنشأ من تطبيق/واجهة العميل؛ لا يوجد إنشاء يدوي كامل من الأدمن في هذا النظام.
     */
    public function create(): Factory|View|Application
    {
        $playStore = Helpers::get_business_settings('play_store_config') ?? [];
        $appStore = Helpers::get_business_settings('app_store_config') ?? [];
        $playStoreLink = is_array($playStore) ? (string) ($playStore['link'] ?? '') : '';
        $appStoreLink = is_array($appStore) ? (string) ($appStore['link'] ?? '') : '';

        return view('admin-views.order.create', compact('playStoreLink', 'appStoreLink'));
    }

    /**
     * @param Request $request
     * @param $status
     * @return Application|Factory|View
     */
    public function list(Request $request, string $status): Factory|View|Application|\Illuminate\Http\JsonResponse
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $query = $this->order->notPos()->with(['customer', 'branch']);

        if ($status !== 'all') {
            $query = $query->where('order_status', $status);
        }

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end   = Carbon::parse($endDate)->endOfDay();
            $query = $query->whereBetween('created_at', [$start, $end]);
        }

        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('order_status', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%");
            });
        }

        $queryParam = collect([
            'search' => $search,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'per_page' => $perPage,
        ])->filter(fn($value) => filled($value))->all();

        $orders = $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($queryParam);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $orders->getCollection()->map(fn ($order) => [
                    'id' => $order->id,
                    'order_status' => $order->order_status,
                    'payment_status' => $order->payment_status,
                    'order_amount' => $order->order_amount,
                    'order_type' => $order->order_type,
                    'created_at' => $order->created_at?->toIso8601String(),
                    'customer' => $order->customer ? [
                        'id' => $order->customer->id,
                        'name' => trim(($order->customer->f_name ?? '') . ' ' . ($order->customer->l_name ?? '')),
                        'phone' => $order->customer->phone ?? null,
                    ] : null,
                    'branch' => $order->branch ? ['id' => $order->branch->id, 'name' => $order->branch->name] : null,
                ]),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ],
                'filters' => [
                    'status' => $status,
                    'search' => $search,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ]);
        }

        return view('admin-views.order.list', compact(
            'orders', 'status', 'search', 'startDate', 'endDate', 'perPage'
        ));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function details($id): View|Factory|RedirectResponse|Application
    {
        $order = $this->order->with('details', 'deliveryAddress', 'orderArea', 'orderShipments.shippingCompany', 'statusLogs')->where(['id' => $id])->first();

        if (!$order) {
            Toastr::info(translate('No more orders!'));
            return back();
        }

        if ((int) $order->checked === 0) {
            $order->checked = 1;
            $order->save();
        }

        $orderedProducts = $this->existedProducts($order);
        $deliveredToLatitude = $deliveredToLongitude = $deliveredFromLatitude = $deliveredFromLongitude = 0;

        // Show real delivery charge (not default 100): use order_area when present, else try delivery address area
        $oldDeliveryCharge = (float) $order->delivery_charge;
        $deliveryChargeDisplay = $oldDeliveryCharge;
        if ($order->order_type !== 'self_pickup') {
            $areaId = $order->orderArea?->area_id ?? $order->deliveryAddress?->area_id;
            $distance = $order->orderArea?->distance ?? 0;
            if ($areaId !== null) {
                try {
                    $recalculated = Helpers::get_delivery_charge(
                        $order->branch_id,
                        $distance,
                        $areaId
                    );
                    $deliveryChargeDisplay = (float) $recalculated;
                    // Persist so order page and DB stay correct (fixes orders saved with wrong 100)
                    if (abs($deliveryChargeDisplay - $oldDeliveryCharge) > 0.001) {
                        $order->delivery_charge = $deliveryChargeDisplay;
                        $order->order_amount = (float) $order->order_amount - $oldDeliveryCharge + $deliveryChargeDisplay;
                        $order->saveQuietly();
                    }
                } catch (\Throwable $e) {
                    // keep current
                }
            }
        }

        $shippingCompanies = \Illuminate\Support\Facades\Schema::hasTable('shipping_companies')
            ? \App\Models\ShippingCompany::active()->orderBy('sort_order')->orderBy('name')->get()
            : collect();
        return view('admin-views.order.order-view', compact('order', 'orderedProducts', 'deliveredToLatitude', 'deliveredToLongitude', 'deliveredFromLatitude', 'deliveredFromLongitude', 'deliveryChargeDisplay', 'shippingCompanies'));
    }

    private function existedProducts(Order $order): Collection
    {
        return $order->details->map(function ($productDetail) {
            $product = $this->product->find($productDetail->product_id);
            if (!$product) {
                return [
                    'id' => $productDetail->product_id,
                    'name' => translate('Product deleted'),
                    'quantity' => $productDetail->quantity,
                    'variant' => '',
                    'base_price' => $productDetail->price ?? 0,
                    'price_with_symbol' => Helpers::set_symbol($productDetail->price ?? 0),
                    'price' => $productDetail->price ?? 0,
                    'discount' => $productDetail->discount_on_product ?? 0,
                    'product_discount' => ($productDetail->price ?? 0) - ($productDetail->discount_on_product ?? 0),
                    'image' => asset('assets/admin/img/160x160/img2.jpg'),
                    'total_stock' => 0,
                    'total_price' => Helpers::set_symbol(($productDetail->price ?? 0) * $productDetail->quantity),
                    'total_discount_price' => Helpers::set_symbol((($productDetail->price ?? 0) - ($productDetail->discount_on_product ?? 0)) * $productDetail->quantity),
                ];
            }

            $variationDecoded = json_decode($productDetail->variation ?? '[]', true);
            $variationArr = is_array($variationDecoded) ? $variationDecoded : [];
            $variantType = $variationArr[0]['type'] ?? ($variationArr['type'] ?? '');
            $productVariation = json_decode($product->variations ?? '[]', true) ?? [];

            $imageFullpath = $product->image_fullpath ?? [];
            $imageUrl = (is_array($imageFullpath) && !empty($imageFullpath)) ? $imageFullpath[0] : asset('assets/admin/img/160x160/img2.jpg');

            $stockFromVariation = !empty($variationArr)
                ? (collect($productVariation)->firstWhere('type', $variantType)['stock'] ?? $product->total_stock)
                : $product->total_stock;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $productDetail->quantity,
                'variant' => $variantType,
                'base_price' => $productDetail->price ?? 0,
                'price_with_symbol' => Helpers::set_symbol($productDetail->price ?? 0),
                'price' => $productDetail->price ?? 0,
                'discount' => $productDetail->discount_on_product ?? 0,
                'product_discount' => ($productDetail->price ?? 0) - ($productDetail->discount_on_product ?? 0),
                'image' => $imageUrl,
                'total_stock' => $stockFromVariation + $productDetail->quantity,
                'total_price' => Helpers::set_symbol(($productDetail->price ?? 0) * $productDetail->quantity),
                'total_discount_price' => Helpers::set_symbol((($productDetail->price ?? 0) - ($productDetail->discount_on_product ?? 0)) * $productDetail->quantity),
            ];
        });
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $order = $this->order->find($request->id);

        if ($order === null) {
            Toastr::error(translate('Order not found!'));
            return back();
        }

        if (in_array($order->order_status, ['returned', 'delivered', 'failed', 'canceled'])) {
            Toastr::warning(translate('you_can_not_change_the_status_of ' . $order->order_status . ' order'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['payment_status'] != 'paid') {
            Toastr::warning(translate('you_can_not_delivered_a_order_when_order_status_is_not_paid. please_update_payment_status_first'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['transaction_reference'] == null && !in_array($order['payment_method'], ['cash_on_delivery', 'wallet'])) {
            Toastr::warning(translate('add_your_payment_reference_first'));
            return back();
        }

        // Store owner handles delivery – no delivery man assignment required
        if ($request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled') {
            foreach ($order->details as $detail) {
                if ((int) $detail['is_stock_decreased'] !== 1) {
                    continue;
                }
                $product = $this->product->find($detail['product_id']);
                if ($product === null) {
                    continue;
                }

                $lineVars = json_decode($detail['variation'] ?? '[]', true);
                if (! is_array($lineVars)) {
                    $lineVars = [];
                }
                $variationType = $this->orderLineVariationType($lineVars);
                $hasVariantLine = $variationType !== null;

                $payload = [
                    'total_stock' => (int) $product['total_stock'] + (int) $detail['quantity'],
                ];

                if ($hasVariantLine) {
                    $prodVars = json_decode($product['variations'] ?? '[]', true);
                    if (! is_array($prodVars)) {
                        $prodVars = [];
                    }
                    $varStore = [];
                    foreach ($prodVars as $var) {
                        if (($var['type'] ?? null) == $variationType) {
                            $var['stock'] = ($var['stock'] ?? 0) + (int) $detail['quantity'];
                        }
                        $varStore[] = $var;
                    }
                    $payload['variations'] = json_encode($varStore);
                }

                $this->product->where(['id' => $product['id']])->update($payload);
                $this->orderDetail->where(['id' => $detail['id']])->update([
                    'is_stock_decreased' => 0,
                ]);
            }
        } else {
            foreach ($order->details as $c) {
                if ((int) $c['is_stock_decreased'] !== 0) {
                    continue;
                }
                $product = $this->product->find($c['product_id']);
                if ($product === null) {
                    continue;
                }

                $lineVars = json_decode($c['variation'] ?? '[]', true);
                if (! is_array($lineVars)) {
                    $lineVars = [];
                }
                $variationType = $this->orderLineVariationType($lineVars);
                $hasVariantLine = $variationType !== null;

                if ($hasVariantLine) {
                    $prodVars = json_decode($product['variations'] ?? '[]', true);
                    if (! is_array($prodVars)) {
                        $prodVars = [];
                    }
                    $enough = false;
                    foreach ($prodVars as $var) {
                        if (($var['type'] ?? null) == $variationType && ($var['stock'] ?? 0) >= (int) $c['quantity']) {
                            $enough = true;
                            break;
                        }
                    }
                    if (! $enough) {
                        Toastr::error(translate('Stock is insufficient!'));
                        return back();
                    }
                } elseif ((int) $product['total_stock'] < (int) $c['quantity']) {
                    Toastr::error(translate('Stock is insufficient!'));
                    return back();
                }
            }

            foreach ($order->details as $detail) {
                if ((int) $detail['is_stock_decreased'] !== 0) {
                    continue;
                }
                $product = $this->product->find($detail['product_id']);
                if ($product === null) {
                    continue;
                }

                $lineVars = json_decode($detail['variation'] ?? '[]', true);
                if (! is_array($lineVars)) {
                    $lineVars = [];
                }
                $variationType = $this->orderLineVariationType($lineVars);
                $hasVariantLine = $variationType !== null;

                $payload = [
                    'total_stock' => max(0, (int) $product['total_stock'] - (int) $detail['quantity']),
                ];

                if ($hasVariantLine) {
                    $prodVars = json_decode($product['variations'] ?? '[]', true);
                    if (! is_array($prodVars)) {
                        $prodVars = [];
                    }
                    $varStore = [];
                    foreach ($prodVars as $var) {
                        if (($var['type'] ?? null) == $variationType) {
                            $var['stock'] = max(0, ($var['stock'] ?? 0) - (int) $detail['quantity']);
                        }
                        $varStore[] = $var;
                    }
                    $payload['variations'] = json_encode($varStore);
                }

                $this->product->where(['id' => $product['id']])->update($payload);
                $this->orderDetail->where(['id' => $detail['id']])->update([
                    'is_stock_decreased' => 1,
                ]);
            }
        }

        $oldStatus = $order->order_status;
        $order->order_status = $request->order_status;
        DB::beginTransaction();
        $order->save();
        \App\Services\OrderStatusLogService::log($order, $oldStatus, $request->order_status);
        DB::commit();

        \App\Services\WebhookService::dispatchOrderStatusChanged($order, $oldStatus, $request->order_status);

        if ($request->order_status === 'delivered') {
            \App\Services\LoyaltyService::awardPointsForDeliveredOrder($order);
        }

        $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
        $value = Helpers::order_status_update_message($request->order_status);
        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order',
                ];
                if ($customerFcmToken != null) {
                    Helpers::send_push_notif_to_device($customerFcmToken, $data);
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed for Customer!'));
        }

        // ── WhatsApp notification via WaSender ──
        $this->sendWhatsAppStatusNotification($order, $request->order_status, $request->input('note'));

        Toastr::success(translate('Order status updated!'));
        return back();
    }

    /**
     * نوع المتغير من سطر الطلب (بدون الوصول إلى $decoded[0] إن لم يوجد — PHP 8 يرفع خطأ).
     *
     * @param  array<int|string, mixed>  $decoded
     */
    private function orderLineVariationType(array $decoded): ?string
    {
        if (array_key_exists(0, $decoded) && is_array($decoded[0])) {
            $t = $decoded[0]['type'] ?? null;
            if (is_string($t) && $t !== '') {
                return $t;
            }
        }
        $t = $decoded['type'] ?? null;
        if (is_string($t) && $t !== '') {
            return $t;
        }

        return null;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function paymentStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'id' => 'required|integer|exists:orders,id',
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        $order = $this->order->find($request->id);
        if (!$order) {
            Toastr::error(translate('Order not found!'));
            return back();
        }

        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery' && $order['payment_method'] != 'wallet') {
            Toastr::warning('Add your payment reference code first!');
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateShipping(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'city' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'city' => $request->city,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success(translate('Address updated!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function generateInvoice($id): View|Factory|Application
    {
        $order = $this->order->with(['orderArea', 'deliveryAddress', 'details.product'])->where('id', $id)->first();
        $deliveryChargeDisplay = $order?->delivery_charge;
        if ($order && $order->order_type !== 'self_pickup') {
            $areaId = $order->orderArea?->area_id ?? $order->deliveryAddress?->area_id;
            if ($areaId !== null) {
                try {
                    $deliveryChargeDisplay = Helpers::get_delivery_charge(
                        $order->branch_id,
                        $order->orderArea?->distance ?? 0,
                        $areaId
                    );
                } catch (\Throwable $e) {
                    // keep existing
                }
            }
        }
        $orderedProducts = $order ? $this->existedProducts($order) : collect();
        return view('admin-views.order.invoice', compact('order', 'deliveryChargeDisplay', 'orderedProducts'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function addPaymentReferenceCode(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'transaction_reference' => 'required|string|max:255',
        ]);

        $this->order->where(['id' => $id])->update([
            'transaction_reference' => $request->transaction_reference
        ]);

        Toastr::success(translate('Payment reference code is added!'));
        return back();
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function branchFilter($id): RedirectResponse
    {
        session()->put('branch_filter', $id);
        return back();
    }


    public function exportOrders(Request $request, string $status): StreamedResponse|string
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Base query with eager loading
        $query = $this->order->notPos()->with(['customer', 'branch']);

        // Status filter
        if ($status !== 'all') {
            $query->where('order_status', $status);
        }

        // Date filter
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Search filter
        if ($search) {
            $keywords = explode(' ', $search);
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('id', 'like', "%{$word}%")
                        ->orWhere('order_status', 'like', "%{$word}%")
                        ->orWhere('payment_status', 'like', "%{$word}%");
                }
            });
        }

        // Exclude POS orders and fetch results
        $orders = $query->orderByDesc('id')->get();

        // Transform data for export
        $storage = $orders->map(function ($order) {
            $branch = $order?->branch?->name ?? 'Branch Deleted';
            $customer = $order->is_guest
                ? 'Guest Customer'
                : ($order->customer
                    ? $order->customer->f_name . ' ' . $order->customer->l_name
                    : 'Customer Deleted');
            return [
                'order_id' => $order->id,
                'customer' => $customer,
                'order_amount' => $order->order_amount,
                'coupon_discount_amount' => $order->coupon_discount_amount,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'total_tax_amount' => $order->total_tax_amount,
                'payment_method' => $order->payment_method,
                'transaction_reference' => $order->transaction_reference,
                'delivery_charge' => $order->delivery_charge,
                'coupon_code' => $order->coupon_code,
                'order_type' => $order->order_type,
                'branch' => $branch,
                'extra_discount' => $order->extra_discount,
            ];
        });

        return (new FastExcel($storage))->download('orders.xlsx');
    }

    public function searchProduct(Request $request): JsonResponse
    {
        $keyword = $request->get('search');
        $products = $this->product->where('status', 1)->where('name', 'like', "%{$keyword}%")
            ->orWhere('id', 'like', "%{$keyword}%")
            ->get();
        $order = $this->order->where(['id' => $request->order_id])->first();
        $existedProducts = $this->existedProducts($order);

        return response()->json([
            'success' => true,
            'view' => view('admin-views.order.partials.product-search-result', compact('products', 'existedProducts'))->render(),
        ]);
    }

    public function updateProductList(Request $request, $id): JsonResponse
    {
        if (!$request->filled('products')) {
            return response()->json(['errors' => [['code' => 'empty-product', 'message' => translate('Product list is empty')]]], 403);
        }
        $order = $this->order->with(['details', 'orderArea'])->where('id', $id)->first();
        $data = [
            'user_id' => $order->user_id,
            'is_guest' => $order->is_guest,
            'coupon_discount_title' => $order->coupon_discount_title,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'coupon_code' => $order->coupon_code,
            'payment_method' => $order->payment_method,
            'transaction_reference' => $order->transaction_reference,
            'order_note' => $order->order_note,
            'order_type' => $order->order_type,
            'branch_id' => $order->branch_id,
            'bring_change_amount' => $order->bring_change_amount,
            'delivery_address_id' => $order->delivery_address_id,
            'delivery_address' => $order->delivery_address,
            'created_at' => $order->created_at,
            'updated_at' => now(),
        ];
        DB::transaction(function () use($request, $order, $data,){
            foreach ($order->details as $existingDetail) {
                $existingProduct = $this->product->find($existingDetail->product_id);
                $quantity = $existingDetail->quantity;
                if (count(json_decode($existingProduct['variations'], true)) > 0) {
                    $existingVariation = json_decode($existingDetail->variation, true);
                    $type = $existingVariation[0]['type'] ?? $existingVariation['type'];
                    $varStore = [];
                    foreach (json_decode($existingProduct['variations'], true) as $var) {
                        if ($var['type'] == $type) {
                            $var['stock'] += $quantity;
                        }
                        $varStore[] = $var;
                    }
                    $this->product->where('id', $existingProduct->id)->update([
                        'variations' => json_encode($varStore),
                        'total_stock' => $existingProduct['total_stock'] + $quantity,
                    ]);
                } else {
                    $this->product->where('id', $existingProduct->id)->update([
                        'total_stock' => $existingProduct['total_stock'] + $quantity,
                    ]);
                }
            }
            $order->details()->delete();
            foreach ($request->products as $product) {
                $existingProduct = $this->product->find($product['id']);
                $variation = [];
                if (count(json_decode($existingProduct['variations'], true)) > 0) {
                    $variation = collect(json_decode($existingProduct['variations'], true))
                        ->where('type', $product['variant'])
                        ->values()
                        ->all();
                    $price = Helpers::variation_price($existingProduct, json_encode($variation));
                } else {
                    $price = $existingProduct['price'];
                }
                $discountOnProduct = Helpers::discount_calculate($existingProduct, $price);
                $taxAmount = Helpers::tax_calculate($existingProduct, $price - $discountOnProduct);
                $orderDetails = [
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'product_details' => $existingProduct,
                    'quantity' => $product['quantity'],
                    'price' => $price,
                    'unit' => $existingProduct['unit'],
                    'tax_amount' => $taxAmount,
                    'discount_on_product' => $discountOnProduct,
                    'discount_type' => 'discount_on_product',
                    'variant' => $product['variant'],
                    'variation' => !empty($variation) ? json_encode($variation[0]) : json_encode([]),
                    'is_stock_decreased' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if (count(json_decode($existingProduct['variations'], true)) > 0) {
                    $type = $variation[0]['type'];
                    $varStore = [];
                    foreach (json_decode($existingProduct['variations'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['stock'] -= $product['quantity'];
                        }
                        $varStore[] = $var;
                    }
                    $this->product->where(['id' => $existingProduct['id']])->update([
                        'variations' => json_encode($varStore),
                        'total_stock' => $existingProduct['total_stock'] - $product['quantity'],
                    ]);
                } else {
                    $this->product->where(['id' => $existingProduct['id']])->update([
                        'total_stock' => $existingProduct['total_stock'] - $product['quantity'],
                    ]);
                }

                DB::table('order_details')->insert($orderDetails);
            }
            $orderCalculation = $this->calculateOrderAmountForEdit(
                $request->products,
                $order->coupon_code ?? null,
                $order->user_id
            );

            $orderAmount = $orderCalculation['order_amount'];
            $couponDiscount = $orderCalculation['coupon_discount'];
            $totalTax = $orderCalculation['total_tax'];
            $deliveryCharge = $order->order_type === 'self_pickup' ? 0 : Helpers::get_delivery_charge(
                branchId: $order->branch_id,
                distance: $order->orderArea?->distance ?? 0,
                selectedDeliveryArea: $order->orderArea?->area_id ?? null
            );
            $data = array_merge($data, [
                'order_amount' => $orderAmount + $deliveryCharge,
                'coupon_discount_amount' => $couponDiscount,
                'total_tax_amount' => $totalTax,
                'delivery_charge' => $deliveryCharge,
            ]);

            $order->update($data);
        });

        return response()->json([
            'status' => true,
            'message' => translate('Order placed successfully'),
            'order_id' => $order->id
        ], 200);
    }

    /**
     * Quick view for order editing (product modal).
     */
    public function quickView(Request $request): JsonResponse
    {
        $product = $this->product->findOrFail($request->product_id);
        if ($request->filled('product_list')) {
            $cart = collect($request->product_list ?? [])->filter(fn($value, $key) => is_array($value))->values();
        } else {
            $cart = collect(session()->get('cart', []))->filter(fn($value, $key) => is_array($value))->values();
        }
        $cartProduct = $cart->where('id', $request->product_id)->values();
        $variations = json_decode($product->variations, true) ?? [];
        $firstVariation = is_array($variations) ? (collect($variations)->first()) : null;
        $productVariation = (is_array($firstVariation) && isset($firstVariation['type'])) ? $firstVariation['type'] : '';
        $quantity = 1;
        $price = 0;
        $stock = !empty($variations) ? collect($variations)->first()['stock'] ?? 0 : $product->total_stock;
        $buttonText = translate('Add to Cart');

        if ($productVariation && is_array($variations)) {
            $matchedVariation = collect($variations)->firstWhere('type', $productVariation);
            if ($matchedVariation) {
                $matchedCart = $cartProduct->firstWhere('variant', $productVariation);
                $stock = $matchedVariation['stock'];
                if ($matchedCart) {
                    $quantity = $matchedCart['quantity'];
                    $price = ($matchedCart['price'] - Helpers::discount_calculate($product, $matchedCart['price'])) * $quantity;
                    $buttonText = translate('Update Cart');
                } else {
                    $price = $matchedVariation['price'] - Helpers::discount_calculate($product, $matchedVariation['price']);
                }
            }
        } elseif ($cartProduct->isNotEmpty()) {
            $quantity = $cartProduct[0]['quantity'];
            $price = ($cartProduct[0]['price'] - Helpers::discount_calculate($product, $cartProduct[0]['price'])) * $quantity;
            $buttonText = translate('Update Cart');
        } else {
            $price = $product->price - Helpers::discount_calculate($product, $product->price);
        }
        return response()->json([
            'success' => 1,
            'view' => view('admin-views.order.partials.quick-view-data', compact('product', 'quantity', 'price', 'stock', 'buttonText'))->render(),
        ]);
    }

    /**
     * Quick view modal footer for order editing.
     */
    public function quickViewModalFooter(Request $request): JsonResponse
    {
        $product = $this->product->findOrFail($request->id);
        if ($request->filled('product_list')) {
            $cart = collect($request->product_list ?? [])->filter(fn($value, $key) => is_array($value))->values();
        } else {
            $cart = collect(session()->get('cart', []))->filter(fn($value, $key) => is_array($value))->values();
        }
        $cartProduct = $cart->where('id', $request->id)->values();
        $str = '';
        $choiceOptions = json_decode($product->choice_options ?? '[]', true);
        if (!empty($choiceOptions) && is_array($choiceOptions)) {
            foreach ($choiceOptions as $key => $choice) {
                $choice = (object) $choice;
                $option = str_replace(' ', '', $request[$choice->name] ?? '');
                $str .= ($str !== '') ? '-' . $option : $option;
            }
        }
        $quantity = 1;
        $price = 0;
        $stock = 0;
        $buttonText = translate('Add to Cart');
        $variations = json_decode($product->variations, true) ?? [];
        if (!empty($str) && is_array($variations)) {
            $matchedVariation = collect($variations)->firstWhere('type', $str);
            if ($matchedVariation) {
                $matchedCart = $cartProduct->firstWhere('variant', $str);
                $stock = $matchedVariation['stock'];
                if ($matchedCart) {
                    $quantity = $matchedCart['quantity'];
                    $price = ($matchedCart['price'] - Helpers::discount_calculate($product, $matchedCart['price'])) * $quantity;
                    $buttonText = translate('Update Cart');
                } else {
                    $price = $matchedVariation['price'] - Helpers::discount_calculate($product, $matchedVariation['price']);
                }
            }
        } else {
            $stock = (int) $product->total_stock;
            $matchedCart = $cartProduct->first();
            if ($matchedCart) {
                $quantity = (int) ($matchedCart['quantity'] ?? 1);
                $price = ($matchedCart['price'] - Helpers::discount_calculate($product, $matchedCart['price'])) * $quantity;
                $buttonText = translate('Update Cart');
            } else {
                $price = $product->price - Helpers::discount_calculate($product, $product->price);
            }
        }
        return response()->json([
            'success' => 1,
            'stock' => $stock,
            'view' => view('admin-views.order.partials.quick-view-modal-footer', compact('quantity', 'price', 'stock', 'buttonText'))->render(),
        ]);
    }

    /**
     * Variant price for order editing.
     */
    public function variantPrice(Request $request): array
    {
        $product = $this->product->find($request->id);
        if (!$product) {
            return ['price' => 0, 'stock' => 0];
        }
        $str = '';
        $price = 0;
        $stock = 0;
        $choiceOptions = json_decode($product->choice_options ?? '[]');
        if (!empty($choiceOptions) && is_array($choiceOptions)) {
            foreach ($choiceOptions as $key => $choice) {
                $choice = is_array($choice) ? (object) $choice : $choice;
                $name = $choice->name ?? '';
                if ($name === '') continue;
                $val = str_replace(' ', '', $request[$name] ?? '');
                $str .= ($str !== '') ? '-' . $val : $val;
            }
        }
        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price - Helpers::discount_calculate($product, json_decode($product->variations)[$i]->price);
                    $stock = json_decode($product->variations)[$i]->stock;
                }
            }
        } else {
            $price = $product->price - Helpers::discount_calculate($product, $product->price);
            $stock = $product->total_stock;
        }
        return ['price' => ($price * $request->quantity), 'stock' => $stock];
    }

    /**
     * Add to cart for order editing (supports product_list from request).
     */
    public function addToCart(Request $request): JsonResponse
    {
        $product = $this->product->find($request->id);
        if (!$product) {
            return response()->json(['data' => 0, 'message' => 'Product not found'], 404);
        }
        $data = [];
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $stock = 0;
        $choiceOptions = json_decode($product->choice_options ?? '[]');
        if (!empty($choiceOptions) && is_array($choiceOptions)) {
            foreach ($choiceOptions as $key => $choice) {
                $choice = is_array($choice) ? (object) $choice : $choice;
                $data[$choice->name] = $request[$choice->name] ?? null;
                $variations[$choice->title ?? $choice->name] = $request[$choice->name] ?? '';
                $val = str_replace(' ', '', $request[$choice->name] ?? '');
                $str .= ($str !== '') ? '-' . $val : $val;
            }
        }
        $data['variations'] = $variations;
        $data['variant'] = $str;
        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price;
                    $stock = json_decode($product->variations)[$i]->stock;
                }
            }
        } else {
            $price = $product->price;
            $stock = $product->total_stock;
        }
        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $data['discount'] = Helpers::discount_calculate($product, $price);
        $data['image'] = $product->image_fullpath;
        $data['total_stock'] = $stock;
        if ($request->filled('product_list')) {
            $cart = $request->product_list ?? [];
        } else {
            $cart = $request->session()->get('cart', []);
        }
        $cart = collect($cart);
        $cartItems = collect($cart)->filter(fn($value, $key) => is_array($value))->values();
        $existingProductKey = $cartItems->search(fn($item) => $item['id'] == $product->id && $item['variant'] == $str);
        if ($existingProductKey !== false) {
            $existingProduct = $cartItems->get($existingProductKey);
            $existingProduct['quantity'] = (int)$request['quantity'];
            if ($existingProduct['quantity'] > $existingProduct['total_stock']) {
                $existingProduct['quantity'] = $existingProduct['total_stock'];
            }
            $cart->put($existingProductKey, $existingProduct);
        } else {
            $cart->push($data);
        }
        if (!$request->filled('product_list')) {
            $request->session()->put('cart', $cart);
            $this->calculatePOSCouponAndExtraDiscount();
        }
        return response()->json(['data' => $data]);
    }

    /**
     * Generate invoice view for modal (order editing print).
     */
    public function generatePosInvoice($id): JsonResponse
    {
        $order = $this->order->where('id', $id)->first();
        return response()->json([
            'success' => 1,
            'view' => view('admin-views.order.partials.invoice-print', compact('order'))->render(),
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    //  DYNAMIC ORDER UPDATE (admin enters any custom status text)
    // ══════════════════════════════════════════════════════════════

    /**
     * Admin adds a free-text status update to the order timeline.
     * POST /admin/orders/{id}/add-update
     */
    public function addCustomStatus(\Illuminate\Http\Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $order = $this->order->findOrFail($id);

        $request->validate([
            'custom_status' => 'required|string|max:255',
            'note'          => 'nullable|string|max:500',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = trim($request->input('custom_status'));
        $note      = $request->input('note');

        // Update the order's current status
        $order->order_status = $newStatus;
        $order->save();

        // Log the change
        \App\Services\OrderStatusLogService::log($order, $oldStatus, $newStatus, $note);

        // Send WhatsApp notification to customer
        $this->sendWhatsAppStatusNotification($order, $newStatus, $note);

        \Brian2694\Toastr\Facades\Toastr::success(translate('order_update_added') ?: 'تم إضافة التحديث وإشعار العميل!');
        return back();
    }

    // ══════════════════════════════════════════════════════════════
    //  WHATSAPP HELPER
    // ══════════════════════════════════════════════════════════════

    /**
     * Send a WhatsApp status-change message to the customer (non-blocking).
     */
    private function sendWhatsAppStatusNotification(Order $order, string $newStatus, ?string $note = null): void
    {
        if (!config('wasender.enabled', false)) {
            return;
        }

        // Resolve customer phone
        $phone = null;

        if ($order->is_guest == 0 && $order->customer) {
            $phone = $order->customer->phone ?? null;
        }

        if (empty($phone) && !empty($order->delivery_address)) {
            $addr = is_array($order->delivery_address)
                ? $order->delivery_address
                : json_decode($order->delivery_address, true);
            $phone = $addr['contact_person_number'] ?? $addr['phone'] ?? null;
        }

        if (empty($phone)) {
            return;
        }

        $storeName = \App\CentralLogics\Helpers::get_business_settings('store_name') ?? 'المتجر';

        $message = WaSenderService::buildOrderStatusMessage(
            orderId:   $order->id,
            newStatus: $newStatus,
            storeName: $storeName,
            note:      $note
        );

        try {
            (new WaSenderService())->sendMessage($phone, $message);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('WaSender order notification: ' . $e->getMessage());
        }
    }
}
