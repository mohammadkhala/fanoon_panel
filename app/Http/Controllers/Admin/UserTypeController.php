<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\Translation;
use App\Models\UserType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserTypeController extends Controller
{
    public function index(): View
    {
        $userTypes = UserType::with('translations')->orderBy('id')->get();
        $languageSetting = BusinessSetting::where('key', 'language')->first();
        $languages = json_decode($languageSetting->value ?? '["ar"]', true) ?: ['ar'];
        $defaultLang = $languages[0] ?? config('app.locale', 'ar');

        return view('admin-views.user-type.index', compact('userTypes', 'languages', 'defaultLang'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name.*' => 'nullable|string|max:255',
        ], [
            'name.*.max' => translate('Name must not exceed 255 characters'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $languages = $request->input('lang', []);
        $names = $request->input('name', []);
        $defaultLang = config('app.locale', 'ar');
        $defaultIndex = array_search($defaultLang, $languages, true);
        if ($defaultIndex === false) {
            $defaultIndex = 0;
        }
        if (trim((string)($names[$defaultIndex] ?? '')) === '') {
            return response()->json(['errors' => [['message' => translate('Name is required!')]]], 403);
        }

        $type = UserType::create([
            'name' => $names[$defaultIndex] ?? $names[0],
            'is_default' => UserType::count() === 0,
        ]);

        $translations = [];
        foreach ($languages as $index => $lang) {
            $value = trim((string)($names[$index] ?? ''));
            if ($value === '' || $lang === $defaultLang) {
                continue;
            }
            $translations[] = [
                'translationable_type' => UserType::class,
                'translationable_id' => $type->id,
                'locale' => $lang,
                'key' => 'name',
                'value' => $value,
            ];
        }

        if ($translations !== []) {
            Translation::insert($translations);
        }

        Toastr::success(translate('User type added successfully!'));
        return back();
    }

    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $type = UserType::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name.*' => 'nullable|string|max:255',
        ], [
            'name.*.max' => translate('Name must not exceed 255 characters'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $languages = $request->input('lang', []);
        $names = $request->input('name', []);
        $defaultLang = config('app.locale', 'ar');
        $defaultIndex = array_search($defaultLang, $languages, true);
        if ($defaultIndex === false) {
            $defaultIndex = 0;
        }
        if (trim((string)($names[$defaultIndex] ?? '')) === '') {
            return response()->json(['errors' => [['message' => translate('Name is required!')]]], 403);
        }

        $type->name = $names[$defaultIndex] ?? $names[0];
        $type->save();

        foreach ($languages as $index => $lang) {
            $value = trim((string)($names[$index] ?? ''));
            if ($lang === $defaultLang) {
                continue;
            }
            if ($value === '') {
                Translation::where('translationable_type', UserType::class)
                    ->where('translationable_id', $type->id)
                    ->where('locale', $lang)
                    ->where('key', 'name')
                    ->delete();
                continue;
            }

            Translation::updateOrInsert(
                [
                    'translationable_type' => UserType::class,
                    'translationable_id' => $type->id,
                    'locale' => $lang,
                    'key' => 'name',
                ],
                ['value' => $value]
            );
        }

        Toastr::success(translate('User type updated successfully!'));
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $type = UserType::findOrFail($id);
        if ($type->is_default && UserType::count() <= 1) {
            Toastr::error(translate('Cannot delete the only default user type!'));
            return back();
        }
        if ($type->users()->exists() || $type->requestedByUsers()->exists()) {
            Toastr::error(translate('Cannot delete user type that is in use!'));
            return back();
        }
        Translation::where('translationable_type', UserType::class)
            ->where('translationable_id', $type->id)
            ->delete();
        $type->productPrices()->delete();
        $type->delete();
        if ($type->is_default) {
            $first = UserType::orderBy('id')->first();
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }
        Toastr::success(translate('User type removed!'));
        return back();
    }

    public function setDefault(int $id): RedirectResponse
    {
        UserType::where('id', '!=', $id)->update(['is_default' => false]);
        UserType::where('id', $id)->update(['is_default' => true]);
        Toastr::success(translate('Default user type updated!'));
        return back();
    }
}
