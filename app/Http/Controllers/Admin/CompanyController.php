<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(): Response
    {
        $companies = CompanyProfile::with('user')->latest()->get()
            ->map(fn (CompanyProfile $c) => [
                'id' => $c->id,
                'company_name' => $c->company_name,
                'contact' => $c->user?->name,
                'email' => $c->user?->email,
                'phone' => $c->user?->phone,
                'city' => $c->city,
                'trade_license_no' => $c->trade_license_no,
                'trade_license_url' => $c->trade_license_path
                    ? asset('storage/' . $c->trade_license_path) : null,
                'status' => $c->status,
                'created_at' => $c->created_at->format('Y-m-d'),
            ]);

        return Inertia::render('Admin/Companies/Index', [
            'companies' => $companies,
        ]);
    }

    /** Full company detail page with statistics. */
    public function show(CompanyProfile $company): Response
    {
        $company->load('user');
        $user = $company->user;

        // All orders for this user
        $orders = $user
            ? $user->orders()->withCount('items')->latest()->get()
            : collect();

        $stats = [
            'total'         => $orders->count(),
            'pending'       => $orders->where('status', 'pending_review')->count(),
            'in_production' => $orders->whereIn('status', ['approved', 'in_production', 'ready'])->count(),
            'delivered'     => $orders->where('status', 'delivered')->count(),
            'cancelled'     => $orders->where('status', 'cancelled')->count(),
            'revenue'       => (float) $orders->where('status', 'delivered')->sum('total'),
            'lifetime'      => (float) $orders->whereNotIn('status', ['cancelled'])->sum('total'),
        ];

        $recentOrders = $orders->take(15)->map(fn (Order $o) => [
            'id'          => $o->id,
            'reference'   => $o->reference,
            'status'      => $o->status,
            'status_label'=> $o->statusLabel(),
            'total'       => (float) $o->total,
            'items_count' => $o->items_count,
            'created_at'  => $o->created_at->format('Y-m-d'),
        ]);

        return Inertia::render('Admin/Companies/Show', [
            'company' => [
                'id'               => $company->id,
                'company_name'     => $company->company_name,
                'status'           => $company->status,
                'trade_license_no' => $company->trade_license_no,
                'trade_license_url'=> $company->trade_license_path
                    ? asset('storage/' . $company->trade_license_path) : null,
                'city'             => $company->city,
                'address'          => $company->address,
                'review_notes'     => $company->review_notes,
                'created_at'       => $company->created_at->format('Y-m-d'),
            ],
            'contact' => $user ? [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'phone'        => $user->phone,
                'member_since' => $user->created_at->format('Y-m-d'),
            ] : null,
            'stats'         => $stats,
            'recent_orders' => $recentOrders,
        ]);
    }

    public function update(Request $request, CompanyProfile $company): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'review_notes' => 'nullable|string|max:2000',
        ]);

        $company->update($data);
        $company->user?->update(['company_status' => $data['status']]);

        return back()->with('success', 'تم تحديث حالة الشركة.');
    }
}
