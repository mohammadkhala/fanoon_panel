<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\OrderShipment;
use App\Models\ShippingCompany;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShippingCompanyController extends Controller
{
    public function __construct(
        private ShippingCompany $shippingCompany,
        private OrderShipment $orderShipment
    ) {}

    public function index(): View|Factory
    {
        $companies = $this->shippingCompany->orderBy('sort_order')->orderBy('name')->paginate(Helpers::getPagination());
        return view('admin-views.shipping-company.index', compact('companies'));
    }

    public function create(): View|Factory
    {
        return view('admin-views.shipping-company.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'api_url' => 'nullable|url|max:500',
            'api_key' => 'nullable|string|max:500',
            'api_secret' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'api_url', 'api_key', 'api_secret', 'is_active']);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = (bool) ($request->is_active ?? true);
        $data['sort_order'] = (int) ($this->shippingCompany->max('sort_order') ?? 0) + 1;

        $this->shippingCompany->create($data);
        Toastr::success(translate('Shipping company added successfully'));
        return redirect()->route('admin.shipping-company.index');
    }

    public function edit(int $id): View|Factory|RedirectResponse
    {
        $company = $this->shippingCompany->findOrFail($id);
        return view('admin-views.shipping-company.edit', compact('company'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $company = $this->shippingCompany->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:100',
            'api_url' => 'nullable|url|max:500',
            'api_key' => 'nullable|string|max:500',
            'api_secret' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'api_url', 'api_key', 'api_secret', 'is_active']);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = (bool) ($request->is_active ?? true);

        $company->update($data);
        Toastr::success(translate('Shipping company updated successfully'));
        return redirect()->route('admin.shipping-company.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $company = $this->shippingCompany->findOrFail($id);
        if ($company->orderShipments()->exists()) {
            Toastr::error(translate('Cannot delete: company has shipments'));
            return back();
        }
        $company->delete();
        Toastr::success(translate('Shipping company removed'));
        return redirect()->route('admin.shipping-company.index');
    }

    public function addShipment(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'shipping_company_id' => 'required|exists:shipping_companies,id',
            'tracking_number' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:pending,shipped,in_transit,delivered,failed',
            'notes' => 'nullable|string|max:500',
        ]);

        $order = \App\Models\Order::findOrFail($id);
        $this->orderShipment->create([
            'order_id' => $id,
            'shipping_company_id' => $request->shipping_company_id,
            'tracking_number' => $request->tracking_number,
            'status' => $request->status ?? 'pending',
            'notes' => $request->notes,
            'shipped_at' => ($request->status === 'shipped' || $request->status === 'in_transit' || $request->status === 'delivered') ? now() : null,
        ]);

        Toastr::success(translate('Shipment added successfully'));
        return redirect()->route('admin.orders.details', $id);
    }

    public function updateShipment(Request $request, int $shipmentId): RedirectResponse
    {
        $shipment = $this->orderShipment->findOrFail($shipmentId);
        $request->validate([
            'tracking_number' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:pending,shipped,in_transit,delivered,failed',
            'notes' => 'nullable|string|max:500',
        ]);

        $data = $request->only(['tracking_number', 'status', 'notes']);
        if (in_array($request->status, ['shipped', 'in_transit', 'delivered']) && !$shipment->shipped_at) {
            $data['shipped_at'] = now();
        }
        $shipment->update($data);

        Toastr::success(translate('Shipment updated successfully'));
        return redirect()->route('admin.orders.details', $shipment->order_id);
    }
}
