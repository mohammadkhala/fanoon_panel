<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrder;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\GuestUser;
use App\Models\LoyaltyPoint;
use App\Models\OrderArea;
use App\Models\User;
use App\Traits\OrderPricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery\Exception;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    use OrderPricing;
    public function __construct(
        private CustomerAddress $customerAddress,
        private Order           $order,
        private OrderDetail     $orderDetail,
        private Product         $product,
        private OrderArea $orderArea
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function trackOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = $this->order->find($request['order_id']);

        if (!$order) {
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        // If phone provided, verify it matches the order
        if (!is_null($request->input('phone'))) {
            $phoneVariants = $this->getPhoneVariants($request->input('phone'));

            $verified = false;
            if ($order->is_guest == 0 && $order->customer) {
                $verified = in_array($order->customer->phone, $phoneVariants)
                    || !empty(array_intersect($phoneVariants, $this->getPhoneVariants($order->customer->phone)));
            } else {
                // Guest: phone in delivery_address JSON OR delivery address relation
                $deliveryPhone = $order->delivery_address['contact_person_number']
                    ?? $order->deliveryAddress?->contact_person_number
                    ?? null;
                if ($deliveryPhone) {
                    $verified = !empty(array_intersect($phoneVariants, $this->getPhoneVariants($deliveryPhone)));
                }
            }

            if (!$verified) {
                return response()->json([
                    'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
            }
        }
        // No phone → allow tracking by order_id alone (public tracking page)

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }

    /**
     * Track all orders belonging to a phone number.
     * POST /api/v1/customer/order/track-by-phone
     */
    public function trackByPhone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phoneVariants = $this->getPhoneVariants($request->input('phone'));

        // Build a LIKE list for MySQL JSON search on delivery_address column
        $jsonLikes = array_map(fn($v) => '%"' . addslashes($v) . '"%', $phoneVariants);

        $orders = Order::with(['statusLogs'])
            ->where(function ($q) use ($phoneVariants, $jsonLikes) {
                // Registered-user orders
                $q->whereHas('customer', fn($c) => $c->whereIn('phone', $phoneVariants));

                // Guest orders — phone inside delivery_address JSON blob
                $q->orWhere(function ($q2) use ($jsonLikes) {
                    foreach ($jsonLikes as $like) {
                        $q2->orWhere('delivery_address', 'LIKE', $like);
                    }
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'No orders found for this phone number.']],
            ], 404);
        }

        return response()->json($orders);
    }

    /**
     * Return all possible stored formats for a Palestinian phone number.
     */
    private function getPhoneVariants(string $phone): array
    {
        $d = preg_replace('/[^\d]/', '', $phone);

        // Strip 00 international prefix
        if (str_starts_with($d, '00')) {
            $d = substr($d, 2);
        }

        // Extract 9-digit core (without country code and without leading 0)
        if (str_starts_with($d, '972') && strlen($d) >= 12)      $core = substr($d, 3);
        elseif (str_starts_with($d, '970') && strlen($d) >= 12)  $core = substr($d, 3);
        elseif (str_starts_with($d, '0') && strlen($d) >= 9)     $core = substr($d, 1);
        elseif (strlen($d) === 9)                                  $core = $d;
        else                                                       $core = $d; // unknown format

        return array_unique([
            '+972' . $core,   // E.164 with +972
            '972'  . $core,   // Without + with 972
            '+970' . $core,   // E.164 with +970
            '970'  . $core,   // Without + with 970
            '0'    . $core,   // Local with leading 0
            $core,            // Bare digits
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function placeOrder(StoreOrder $request): JsonResponse
    {
        try{
            try {
                $userId = auth('api')->id();
            } catch (\Throwable $e) {
                $userId = null;
            }
            $userId = $userId ?? config('guest_id');
            $isGuest = !$userId || $userId == config('guest_id');
            $loyaltyPointsUsed = (int) ($request->loyalty_points_used ?? 0);

            if ($loyaltyPointsUsed > 0 && $isGuest) {
                throw ValidationException::withMessages([
                    'loyalty_points_used' => [translate('Guest users cannot use loyalty points')]
                ]);
            }

            $customerIdForPricing = $isGuest ? null : $userId;
            $orderCalculation = $this->calculateOrderAmount(
                $request['cart'],
                $request->coupon_code ?? null,
                $customerIdForPricing,
                $loyaltyPointsUsed
            );

            $orderAmount = $orderCalculation['order_amount'];
            $couponDiscount = $orderCalculation['coupon_discount'];
            $loyaltyDiscount = $orderCalculation['loyalty_discount'] ?? 0;
            $effectiveLoyaltyPointsUsed = $orderCalculation['loyalty_points_used'] ?? $loyaltyPointsUsed;
            $userType = $isGuest ? 1 : 0;
            $branchId = config('feature_flags.single_branch_mode', true)
                ? Helpers::getDefaultBranchId()
                : (int) ($request['branch_id'] ?? Helpers::getDefaultBranchId());
            $deliveryCharge = $request['order_type'] === 'self_pickup' ? 0 : Helpers::get_delivery_charge(
                    branchId: $branchId,
                    distance: $request['distance'] ?? 0,
                    selectedDeliveryArea: $request['selected_delivery_area'] ?? null
                );
            $orderData = [
                'user_id' => $userId,
                'is_guest' => $userType,
                'order_amount' => $orderAmount + $deliveryCharge,
                'coupon_discount_amount' => $couponDiscount,
                'loyalty_points_used' => $effectiveLoyaltyPointsUsed > 0 ? $effectiveLoyaltyPointsUsed : 0,
                'loyalty_discount_amount' => $loyaltyDiscount > 0 ? $loyaltyDiscount : 0,
                'coupon_discount_title' => $request->coupon_discount_title == 0 ? null : 'coupon_discount_title',
                'coupon_code' => $request['coupon_code'] ?? null,
                'payment_status' => $request['payment_method'] === 'cash_on_delivery' ? 'unpaid' : 'paid',
                'order_status' => $request['payment_method'] === 'cash_on_delivery' ? 'pending' : 'confirmed',
                'total_tax_amount' => $orderCalculation['total_tax'],
                'payment_method' => $request['payment_method'],
                'transaction_reference' => $request->transaction_reference ?? null,
                'order_note' => $request['order_note'] ?? null,
                'order_type' => $request['order_type'],
                'branch_id' => $branchId,
                'bring_change_amount' => $request['bring_change_amount'],
                'delivery_address_id' => $request['delivery_address_id'] ?? null,
                'delivery_charge' => $deliveryCharge,
                'delivery_address' => $this->customerAddress->find($request->delivery_address_id)
                    ?? $request->input('delivery_address'),   // fallback للزوار بدون حساب
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::beginTransaction();
            $order = $this->order->create($orderData);
            $orderUser = $userId && $userId != config('guest_id') ? User::find($userId) : null;
            foreach ($request['cart'] as $c) {
                $product = $this->product->find($c['product_id']);
                $variation = $c['variation'] ?? [];
                $variationData = is_array($variation) ? $variation : [];
                $price = Helpers::product_price_for_user(
                    $product,
                    $orderUser,
                    !empty($variationData) ? $variationData : null
                );
                $discountAmount = Helpers::discount_calculate($product, $price);
                $taxAmount = Helpers::tax_calculate($product, $price - $discountAmount);

                $variantType = !empty($variationData) && isset($variationData[0]['type']) ? $variationData[0]['type'] : null;
                $variantJson = !empty($variationData) ? json_encode($variationData[0]) : json_encode($variationData);

                DB::table('order_details')->insert([
                    'order_id' => $order->id,
                    'product_id' => $c['product_id'],
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $price,
                    'unit' => $product['unit'],
                    'tax_amount' => $taxAmount,
                    'discount_on_product' => $discountAmount,
                    'discount_type' => 'discount_on_product',
                    'variant' => $variantType,
                    'variation' => $variantJson,
                    'design_image' => $c['design_image'] ?? null,
                    'design_json' => $c['design_json'] ?? null,
                    'design_width' => $c['design_width'] ?? null,
                    'design_height' => $c['design_height'] ?? null,
                    'is_stock_decreased' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (!empty(json_decode($product->variations, true))) {
                    $type = $variantType;
                    $varStore = [];
                    foreach (json_decode($product->variations, true) as $var) {
                        if ($type == $var['type']) {
                            $var['stock'] -= $c['quantity'];
                        }
                        $varStore[] = $var;
                    }
                    $this->product->where('id', $product->id)->update([
                        'variations' => json_encode($varStore),
                        'total_stock' => $product->total_stock - $c['quantity']
                    ]);
                } else {
                    $this->product->where('id', $product->id)->update([
                        'total_stock' => $product->total_stock - $c['quantity']
                    ]);
                }
            }

            if ($request['selected_delivery_area']) {
                $orderArea = $this->orderArea;
                $orderArea->order_id = $order->id;
                $orderArea->branch_id = $branchId;
                $orderArea->area_id = $request['selected_delivery_area'];
                $orderArea->save();
            }

            DB::commit();

            Cache::forget('admin_store_data');
            if (!empty($order->branch_id)) {
                Cache::forget('branch_store_data_'.$order->branch_id);
            }

            \App\Services\WebhookService::dispatchOrderCreated($order);

            if ($effectiveLoyaltyPointsUsed > 0 && !$isGuest && $orderUser) {
                LoyaltyPoint::deductPointsForOrder(
                    $orderUser,
                    $order->id,
                    $effectiveLoyaltyPointsUsed,
                    $loyaltyDiscount
                );
            }

            if (!$isGuest && $orderUser) {
                $fcmToken = $orderUser->cm_firebase_token ?? '';
            } else {
                $guest = GuestUser::find(config('guest_id'));
                $fcmToken = $guest ? $guest->fcm_token : '';
            }
            $value = Helpers::order_status_update_message('pending');
            $emailServices = Helpers::get_business_settings('mail_config');
            if (isset($emailServices['status']) && $emailServices['status'] == 1 && !$isGuest && $orderUser && $value) {
                try {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $order->id,
                        'image' => '',
                        'type' => 'order',
                    ];
                    Helpers::send_push_notif_to_device($fcmToken, $data);
                    Mail::to($orderUser->email)->send(new \App\Mail\OrderPlaced($order->id));
                } catch (\Throwable $mailException) {
                }
            }

        }  catch (ValidationException $e) {
            $errors = collect($e->errors())->map(function($messages, $field) {
                return [
                    'code' => $field,
                    'message' => $messages[0] ?? ''
                ];
            })->values();

            return response()->json(['errors' => $errors], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => [['code' => 'order', 'message' => translate('Order placement failed. Please try again.')]]], 500);
        }

        return response()->json([
            'message' => translate('Order placed successfully'),
            'order_id' => $order->id
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderList(Request $request): JsonResponse
    {
        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        $orders = $this->order->with(['customer', 'details'])
            ->withCount('details')
            ->where(['user_id' => $userId, 'is_guest' => $userType])
            ->get();

        $orders->each(function ($order) {
            $order->total_quantity = $order->details->sum('quantity');
        });

        return response()->json($orders->map(function ($data) {
            $data->details_count = (integer)$data->details_count;
            return $data;
        }), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;
        $phone = $request->input('phone');

        $order = $this->order->find($request['order_id']);
        if (!isset($order)){
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 403);
        }

        if (!is_null($phone)){
            if ($order['is_guest'] == 0){
                $details = $this->orderDetail->with(['order'])
                    ->where(['order_id' => $request['order_id']])
                    ->whereHas('order.customer', function ($customerSubQuery) use ($phone) {
                        $customerSubQuery->where('phone', $phone);
                    })
                    ->get();
            }else{
                $details = $this->orderDetail->with(['order'])
                    ->where(['order_id' => $request['order_id']])
                    ->whereHas('order.deliveryAddress', function ($addressSubQuery) use ($phone) {
                        $addressSubQuery->where('contact_person_number', $phone);
                    })
                    ->get();
            }
        }else{
            $details = $this->orderDetail->with(['order'])
                ->where(['order_id' => $request['order_id']])
                ->whereHas('order', function ($q) use ($userId, $userType) {
                    $q->where(['user_id' => $userId, 'is_guest' => $userType]);
                })
                ->get();
        }

        if ($details->count() < 1) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('Order not found!')]
                ]
            ], 404);
        }

        $details = Helpers::order_details_formatter($details);
        return response()->json($details, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelOrder(Request $request): JsonResponse
    {
        $order = $this->order::find($request['order_id']);

        if (!isset($order)) {
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        if ($order->order_status != 'pending') {
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order can only cancel when order status is pending!']]], 403);
        }

        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        if ($this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first()) {

            $order = $this->order->with(['details'])->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first();

            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = $this->product->find($detail['product_id']);

                    if ($product) {
                        if (count(json_decode($product['variations'], true)) > 0) {
                            $variation = json_decode($detail['variation'], true);
                            $type = $variation[0]['type'] ?? $variation['type'];
                            $varStore = [];
                            foreach (json_decode($product['variations'], true) as $var) {
                                if ($type == $var['type']) {
                                    $var['stock'] += $detail['quantity'];
                                }
                                $varStore[] = $var;
                            }
                            $this->product->where(['id' => $product['id']])->update([
                                'variations' => json_encode($varStore),
                                'total_stock' => $product['total_stock'] + $detail['quantity'],
                            ]);
                        } else {
                            $this->product->where(['id' => $product['id']])->update([
                                'total_stock' => $product['total_stock'] + $detail['quantity'],
                            ]);
                        }
                    }

                    $this->orderDetail->where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 0
                    ]);
                }
            }

            $this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->update([
                'order_status' => 'canceled',
            ]);
            return response()->json(['message' => translate('Order canceled')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('not found')]
            ]
        ], 401);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePaymentMethod(Request $request): JsonResponse
    {
        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        if ($this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first()) {
            $this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->update([
                'payment_method' => $request['payment_method']
            ]);
            return response()->json(['message' => translate('Payment method is updated.')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('not found')]
            ]
        ], 401);
    }

    public function getReorderProduct(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = $this->orderDetail
            ->where(['order_id' => $request['order_id']])
            ->get();

        if ($details->count() < 1) {
            return response()->json([
                'errors' => [['code' => 'order', 'message' => translate('Order not found!')]]], 404);
        }

        $details = Helpers::order_details_formatter($details);

        $orderProductIds = $this->orderDetail
            ->where(['order_id' => $request->order_id])
            ->pluck('product_id')
            ->toArray();

        $products = $this->product
            ->whereIn('id', $orderProductIds)
            ->latest()
            ->get();

        $products = Helpers::product_data_formatting($products, true);

        $data = [
            'order_details' => $details,
            'products' => $products
        ];

        return response()->json($data, 200);

    }
}
