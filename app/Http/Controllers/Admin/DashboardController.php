<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Order;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $statusCounts = [];
        foreach (Order::STATUSES as $key => $label) {
            $statusCounts[] = [
                'key' => $key,
                'label' => $label,
                'count' => Order::where('status', $key)->count(),
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'pending_orders' => Order::where('status', 'pending_review')->count(),
                'total_orders' => Order::count(),
                'pending_companies' => User::where('account_type', 'company')
                    ->where('company_status', 'pending')->count(),
                'customers' => User::where('account_type', '!=', 'admin')->count(),
                'revenue' => (float) Order::where('status', '!=', 'cancelled')->sum('total'),
            ],
            'statusCounts' => $statusCounts,
            'recentOrders' => Order::with('user')->latest()->take(6)->get()
                ->map(fn (Order $o) => [
                    'id' => $o->id,
                    'reference' => $o->reference,
                    'customer' => $o->user?->name,
                    'status' => $o->status,
                    'status_label' => $o->statusLabel(),
                    'total' => (float) $o->total,
                    'created_at' => $o->created_at->format('Y-m-d'),
                ]),
            'pendingCompanies' => CompanyProfile::with('user')
                ->where('status', 'pending')->latest()->take(5)->get()
                ->map(fn (CompanyProfile $c) => [
                    'id' => $c->id,
                    'company_name' => $c->company_name,
                    'contact' => $c->user?->name,
                    'city' => $c->city,
                    'created_at' => $c->created_at->format('Y-m-d'),
                ]),
        ]);
    }
}
