<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/DeliveryZones/Index', [
            'zones' => DeliveryZone::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        DeliveryZone::create($this->validated($request));

        return back()->with('success', 'تمت إضافة المنطقة.');
    }

    public function update(Request $request, DeliveryZone $zone)
    {
        $zone->update($this->validated($request));

        return back()->with('success', 'تم تحديث المنطقة.');
    }

    public function toggle(DeliveryZone $zone)
    {
        $zone->update(['is_active' => ! $zone->is_active]);

        return back();
    }

    public function destroy(DeliveryZone $zone)
    {
        $zone->delete();

        return back()->with('success', 'تم حذف المنطقة.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'fee' => 'required|numeric|min:0',
            'eta' => 'nullable|string|max:60',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
    }
}
