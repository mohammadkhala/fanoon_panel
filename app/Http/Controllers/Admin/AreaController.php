<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Area;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AreaController extends Controller
{
    public function __construct(private Area $area) {}

    /**
     * @return View|Factory
     */
    public function index(): View|Factory
    {
        if (!Schema::hasTable('areas')) {
            $areas = new \Illuminate\Pagination\LengthAwarePaginator([], 0, Helpers::getPagination());
            return view('admin-views.business-settings.areas-index', compact('areas'));
        }
        $query = $this->area->orderBy('sort_order')->orderBy('name_ar');
        if (Schema::hasColumn('areas', 'branch_id') && config('feature_flags.single_branch_mode', true)) {
            $query->where('branch_id', Helpers::getDefaultBranchId());
        }
        $areas = $query->paginate(Helpers::getPagination());
        return view('admin-views.business-settings.areas-index', compact('areas'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('areas')) {
            Toastr::error(translate('Run migrations to create areas table'));
            return back();
        }
        $name = $request->input('name', []);
        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:100'],
            'delivery_charge' => ['required', 'numeric', 'min:0'],
        ], [
            'name.required' => translate('Area name is required'),
            'delivery_charge.required' => translate('Delivery charge is required'),
        ]);

        $maxOrder = (int) $this->area->max('sort_order');
        $names = is_array($name) ? array_filter($name, fn ($v) => $v !== null && (string) $v !== '') : [];
        if ($this->hasDuplicateAreaName($names)) {
            Toastr::error(translate('Area already exists'));
            return back()->withInput();
        }

        $data = [
            'names' => $names,
            'name_en' => $name['en'] ?? '',
            'name_ar' => $name['ar'] ?? '',
            'delivery_charge' => (float) $request->delivery_charge,
            'sort_order' => $maxOrder + 1,
        ];
        if (Schema::hasColumn('areas', 'branch_id')) {
            $data['branch_id'] = Helpers::getDefaultBranchId();
        }
        $this->area->create($data);

        Toastr::success(translate('Area added successfully'));
        return back();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $area = $this->area->findOrFail($id);
        $name = $request->input('name', []);
        $request->validate([
            'name' => ['required', 'array'],
            'name.*' => ['nullable', 'string', 'max:100'],
            'delivery_charge' => ['required', 'numeric', 'min:0'],
        ]);

        $names = is_array($name) ? array_filter($name, fn ($v) => $v !== null && (string) $v !== '') : [];
        if ($this->hasDuplicateAreaName($names, $id)) {
            Toastr::error(translate('Area already exists'));
            return back()->withInput();
        }

        $area->update([
            'names' => !empty($names) ? $names : $area->names,
            'name_en' => $name['en'] ?? $area->name_en,
            'name_ar' => $name['ar'] ?? $area->name_ar,
            'delivery_charge' => (float) $request->delivery_charge,
        ]);

        Toastr::success(translate('Area updated successfully'));
        return back();
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $area = $this->area->findOrFail($id);
        $area->delete();
        Toastr::success(translate('Area removed'));
        return back();
    }

    /**
     * Prevent duplicate area names per branch.
     */
    private function hasDuplicateAreaName(array $names, ?int $excludeId = null): bool
    {
        $normalized = [];
        foreach ($names as $lang => $value) {
            $clean = trim((string) $value);
            if ($clean !== '') {
                $normalized[(string) $lang] = $clean;
            }
        }

        if ($normalized === []) {
            return false;
        }

        $query = $this->area->newQuery();
        if (Schema::hasColumn('areas', 'branch_id')) {
            $query->where('branch_id', Helpers::getDefaultBranchId());
        }
        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $query->where(function ($q) use ($normalized) {
            foreach ($normalized as $lang => $value) {
                $lowerValue = Str::lower($value);
                if ($lang === 'en') {
                    $q->orWhereRaw('LOWER(TRIM(name_en)) = ?', [$lowerValue]);
                    continue;
                }
                if ($lang === 'ar') {
                    $q->orWhereRaw('LOWER(TRIM(name_ar)) = ?', [$lowerValue]);
                    continue;
                }
                $q->orWhereRaw(
                    "LOWER(TRIM(JSON_UNQUOTE(JSON_EXTRACT(names, '$.\"{$lang}\"')))) = ?",
                    [$lowerValue]
                );
            }
        });

        return $query->exists();
    }
}
