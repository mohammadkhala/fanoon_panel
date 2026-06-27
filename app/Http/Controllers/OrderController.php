<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = $request->user()->orders()
            ->withCount('items')
            ->latest()
            ->get()
            ->map(fn (Order $o) => [
                'id' => $o->id,
                'reference' => $o->reference,
                'status' => $o->status,
                'status_label' => $o->statusLabel(),
                'total' => (float) $o->total,
                'items_count' => $o->items_count,
                'created_at' => $o->created_at->format('Y-m-d'),
            ]);

        return Inertia::render('Orders/Index', ['orders' => $orders]);
    }

    public function show(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items');

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'reference' => $order->reference,
                'status' => $order->status,
                'status_label' => $order->statusLabel(),
                'subtotal'              => (float) $order->subtotal,
                'delivery_fee'          => (float) ($order->delivery_fee ?? 0),
                'total'                 => (float) $order->total,
                'contact_name'          => $order->contact_name,
                'contact_phone'         => $order->contact_phone,
                'contact_email'         => $order->contact_email,
                'shipping_city'         => $order->shipping_city,
                'shipping_address'      => $order->shipping_address,
                'shipping_neighborhood' => $order->shipping_neighborhood,
                'shipping_building'     => $order->shipping_building,
                'payment_method'        => $order->payment_method,
                'notes'                 => $order->notes,
                'created_at'            => $order->created_at->format('Y-m-d H:i'),
                'items' => $order->items->map(fn (OrderItem $i) => [
                    'title' => $i->title,
                    'preview' => $i->preview_path,
                    'quantity' => $i->quantity,
                    'unit_price' => (float) $i->unit_price,
                    'line_total' => (float) $i->line_total,
                ]),
            ],
        ]);
    }

    /** Render the dedicated checkout page. */
    public function checkoutPage(Request $request): Response|RedirectResponse
    {
        $user  = $request->user();
        $items = CartController::items($user);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'سلتك فارغة.');
        }

        return Inertia::render('Checkout/Index', [
            'items'    => $items,
            'subtotal' => $items->sum('line_total'),
            'tier'     => $user->pricingTier(),
            'zones'    => \App\Models\DeliveryZone::where('is_active', true)
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->get(['id', 'name', 'fee', 'eta']),
            'user'     => [
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ],
        ]);
    }

    /** Checkout: turn the cart into an order pending admin review. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'            => 'required|string|max:100',
            'last_name'             => 'required|string|max:100',
            'contact_phone'         => 'required|string|max:30',
            'contact_email'         => 'nullable|email|max:200',
            'delivery_zone_id'      => 'nullable|exists:delivery_zones,id',
            'shipping_city'         => 'required|string|max:100',
            'shipping_address'      => 'required|string|max:255',
            'shipping_neighborhood' => 'nullable|string|max:100',
            'shipping_building'     => 'nullable|string|max:100',
            'payment_method'        => 'required|in:cod',
            'notes'                 => 'nullable|string|max:2000',
        ]);

        $user  = $request->user();
        $items = CartController::items($user);

        if ($items->isEmpty()) {
            return back()->with('error', 'سلتك فارغة.');
        }

        $subtotal     = $items->sum('line_total');
        $deliveryFee  = 0;

        if (!empty($data['delivery_zone_id'])) {
            $zone = \App\Models\DeliveryZone::find($data['delivery_zone_id']);
            $deliveryFee = $zone ? (float) $zone->fee : 0;
        }

        $order = DB::transaction(function () use ($user, $items, $data, $subtotal, $deliveryFee) {
            $order = Order::create([
                'user_id'               => $user->id,
                'delivery_zone_id'      => $data['delivery_zone_id'] ?? null,
                'reference'             => 'ELT-' . strtoupper(Str::random(8)),
                'status'                => 'pending_review',
                'pricing_tier'          => $user->pricingTier(),
                'subtotal'              => $subtotal,
                'delivery_fee'          => $deliveryFee,
                'total'                 => $subtotal + $deliveryFee,
                'contact_name'          => trim($data['first_name'] . ' ' . $data['last_name']),
                'contact_phone'         => $data['contact_phone'],
                'contact_email'         => $data['contact_email'] ?? null,
                'shipping_city'         => $data['shipping_city'],
                'shipping_address'      => $data['shipping_address'],
                'shipping_neighborhood' => $data['shipping_neighborhood'] ?? null,
                'shipping_building'     => $data['shipping_building'] ?? null,
                'payment_method'        => $data['payment_method'],
                'notes'                 => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'design_id'   => $item['design_id'],
                    'template_id' => $item['template_id'],
                    'title'       => $item['title'],
                    'preview_path'=> $item['preview'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'line_total'  => $item['line_total'],
                ]);
            }

            CartItem::where('user_id', $user->id)->delete();

            return $order;
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'تم استلام طلبك! ستراجع الإدارة تصميمك وتتواصل معك لإتمام الصنع.');
    }
}
