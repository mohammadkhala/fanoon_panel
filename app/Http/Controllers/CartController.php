<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $items = $this->items($user);

        return Inertia::render('Cart/Index', [
            'items' => $items,
            'subtotal' => $items->sum('line_total'),
            'tier' => $user->pricingTier(),
        ]);
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $data = $request->validate(['quantity' => 'required|integer|min:1|max:999']);
        $cartItem->update(['quantity' => $data['quantity']]);

        return back();
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $cartItem->delete();

        return back()->with('success', 'تمت إزالة العنصر من السلة.');
    }

    /** @return \Illuminate\Support\Collection */
    public static function items($user)
    {
        return CartItem::with(['design', 'template', 'productTemplate.product', 'product'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function (CartItem $item) use ($user) {
                // Resolve price: old Template → ProductTemplate.product → direct Product
                $price = 0;
                if ($item->template) {
                    $price = $item->template->priceFor($user);
                } elseif ($item->productTemplate?->product) {
                    $price = $item->productTemplate->product->priceFor($user);
                } elseif ($item->product) {
                    $price = $item->product->priceFor($user);
                }

                // Resolve title
                $title = $item->template?->name
                    ?? $item->productTemplate?->product?->name
                    ?? $item->product?->name
                    ?? $item->design?->name
                    ?? 'تصميم مخصّص';

                return [
                    'id'          => $item->id,
                    'title'       => $title,
                    'preview'     => $item->design?->preview_path,
                    'design_id'   => $item->design_id,
                    'template_id' => $item->template_id,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $price,
                    'line_total'  => $price * $item->quantity,
                    'is_direct'   => ! $item->template_id && ! $item->product_template_id,
                ];
            });
    }
}
