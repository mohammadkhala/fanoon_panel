<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Admin/Settings/Edit', [
            'settings' => Setting::values(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:40',
            'whatsapp' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:120',
            'address' => 'nullable|string|max:200',
            'working_hours' => 'nullable|string|max:120',
            'delivery_individual' => 'nullable|string|max:80',
            'delivery_company' => 'nullable|string|max:80',
            'instagram' => 'nullable|string|max:200',
            'facebook' => 'nullable|string|max:200',
            'tiktok' => 'nullable|string|max:200',
            'announcement' => 'nullable|string|max:200',
            'store_open' => 'boolean',
        ]);

        $data['store_open'] = $request->boolean('store_open') ? '1' : '0';

        Setting::setMany($data);

        return back()->with('success', 'تم حفظ إعدادات المتجر.');
    }
}
