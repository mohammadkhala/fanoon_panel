<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Area;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function __construct(private City $city) {}

    /**
     * @return View|Factory
     */
    public function index(): View|Factory
    {
        $hasAreas = Schema::hasTable('areas');
        $cities = $this->city
            ->when($hasAreas && Schema::hasColumn('cities', 'area_id'), fn ($q) => $q->with('area:id,name_en,name_ar,names'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(Helpers::getPagination());
        $areas = $hasAreas ? Area::orderBy('sort_order')->orderBy('name_ar')->get(['id', 'name_en', 'name_ar', 'names']) : collect();
        return view('admin-views.business-settings.cities-index', compact('cities', 'areas'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $name = $request->input('name', []);
        $rules = [
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:100'],
        ];
        if (Schema::hasTable('areas')) {
            $rules['area_id'] = ['nullable', 'exists:areas,id'];
        }
        $request->validate($rules, [
            'name.required' => translate('City name is required'),
        ]);

        $maxOrder = (int) $this->city->max('sort_order');
        $names = is_array($name) ? array_filter($name, fn ($v) => $v !== null && (string) $v !== '') : [];
        $data = [
            'names' => $names,
            'name' => $name['en'] ?? $name['ar'] ?? array_values($names)[0] ?? '',
            'name_ar' => $name['ar'] ?? '',
            'sort_order' => $maxOrder + 1,
        ];
        if (Schema::hasColumn('cities', 'area_id')) {
            $data['area_id'] = $request->area_id ?: null;
        }
        $this->city->create($data);

        Toastr::success(translate('City added successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $city = $this->city->findOrFail($id);
        $name = $request->input('name', []);
        $rules = [
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:100'],
        ];
        if (Schema::hasTable('areas')) {
            $rules['area_id'] = ['nullable', 'exists:areas,id'];
        }
        $request->validate($rules);

        $names = is_array($name) ? array_filter($name, fn ($v) => $v !== null && (string) $v !== '') : [];
        $data = [
            'names' => !empty($names) ? $names : $city->names,
            'name' => $name['en'] ?? $city->name,
            'name_ar' => $name['ar'] ?? $city->name_ar,
        ];
        if (Schema::hasColumn('cities', 'area_id')) {
            $data['area_id'] = $request->area_id ?: null;
        }
        $city->update($data);
        Toastr::success(translate('City updated successfully'));
        return back();
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $city = $this->city->findOrFail($id);
        $city->delete();
        Toastr::success(translate('City removed'));
        return back();
    }
}
