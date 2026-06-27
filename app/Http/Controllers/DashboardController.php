<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $orders = $user->orders()->withCount('items')->latest()->get();

        $stats = [
            'total'        => $orders->count(),
            'pending'      => $orders->where('status', 'pending_review')->count(),
            'in_production'=> $orders->whereIn('status', ['approved', 'in_production', 'ready'])->count(),
            'delivered'    => $orders->where('status', 'delivered')->count(),
            'cancelled'    => $orders->where('status', 'cancelled')->count(),
            'cart_items'   => $user->cartItems()->count(),
        ];

        $recent = $orders->take(5)->map(fn (Order $o) => [
            'id'          => $o->id,
            'reference'   => $o->reference,
            'status'      => $o->status,
            'status_label'=> $o->statusLabel(),
            'total'       => (float) $o->total,
            'items_count' => $o->items_count,
            'created_at'  => $o->created_at->format('Y-m-d'),
        ]);

        return Inertia::render('Dashboard', [
            'stats'         => $stats,
            'recent_orders' => $recent,
            'profile'       => [
                'name'           => $user->name,
                'email'          => $user->email,
                'phone'          => $user->phone,
                'account_type'   => $user->account_type,
                'company_status' => $user->company_status,
                'tier'           => $user->pricingTier(),
                'member_since'   => $user->created_at->format('Y'),
            ],
        ]);
    }
}
