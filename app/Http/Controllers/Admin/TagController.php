<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function __construct(
        private Tag $tag,
        private Translation $translation
    ) {}

    public function list(Request $request): View|Factory
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $search = $request->query('search');

        $query = $this->tag->newQuery()->with('translations');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('value', 'like', "%{$search}%"));
            });
        }
        $tags = $query->orderBy('sort_order')->orderBy('name')->paginate($perPage)->appends(compact('search', 'perPage'));

        $language = \App\Models\BusinessSetting::where('key', 'language')->first()?->value ?? null;
        $defaultLang = $language ? json_decode($language)[0] ?? 'en' : 'en';

        return view('admin-views.tag.list', compact('tags', 'search', 'perPage', 'language', 'defaultLang'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $names = is_array($request->name ?? null) ? $request->name : [$request->name ?? ''];
        $langs = $request->lang ?? ['en'];
        $defaultLang = $langs[0] ?? 'en';
        $nameIndex = array_search($defaultLang, $langs);
        $nameValue = ($nameIndex !== false && !empty(trim($names[$nameIndex] ?? '')))
            ? trim($names[$nameIndex])
            : null;
        if ($nameValue === null) {
            foreach ($names as $n) {
                if (!empty(trim($n ?? ''))) {
                    $nameValue = trim($n);
                    break;
                }
            }
        }
        $nameValue = $nameValue ?? $names[0] ?? '';

        $validator = Validator::make(array_merge($request->all(), ['name.0' => $nameValue]), [
            'name.0' => 'required|string|max:100|unique:tags,name',
        ], [
            'name.0.required' => translate('product_tags') . ' ' . translate('name') . ' ' . translate('is required'),
            'name.0.unique' => translate('product_tags') . ' ' . translate('already exists'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 422);
            }
            return back()->withErrors($validator);
        }

        $tag = $this->tag->create([
            'name' => $nameValue,
            'slug' => Str::slug($nameValue),
            'sort_order' => (int) ($request->sort_order ?? 0),
        ]);

        $data = [];
        foreach ($langs as $index => $key) {
            $val = trim($names[$index] ?? '');
            if ($val !== '' && $key !== $defaultLang) {
                $data[] = [
                    'translationable_type' => Tag::class,
                    'translationable_id' => $tag->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $val,
                ];
            }
        }
        if (count($data) > 0) {
            $this->translation->insert($data);
        }

        Toastr::success(translate('tag_added_successfully') ?: 'Tag added successfully');
        return back();
    }

    public function update(Request $request, int $id): RedirectResponse|JsonResponse
    {
        $tag = $this->tag->with('translations')->findOrFail($id);

        $names = is_array($request->name ?? null) ? $request->name : [$request->name ?? $tag->getRawOriginal('name')];
        $langs = $request->lang ?? ['en'];
        $defaultLang = $langs[0] ?? 'en';
        $nameIndex = array_search($defaultLang, $langs);
        $nameValue = ($nameIndex !== false && !empty(trim($names[$nameIndex] ?? '')))
            ? trim($names[$nameIndex])
            : null;
        if ($nameValue === null) {
            foreach ($names as $n) {
                if (!empty(trim($n ?? ''))) {
                    $nameValue = trim($n);
                    break;
                }
            }
        }
        $nameValue = $nameValue ?? $names[0] ?? $tag->getRawOriginal('name') ?? '';

        $validator = Validator::make(['name.0' => $nameValue], [
            'name.0' => 'required|string|max:100|unique:tags,name,' . $id,
        ], [
            'name.0.required' => translate('product_tags') . ' ' . translate('name') . ' ' . translate('is required'),
            'name.0.unique' => translate('product_tags') . ' ' . translate('already exists'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 422);
            }
            return back()->withErrors($validator);
        }

        $tag->update([
            'name' => $nameValue,
            'slug' => Str::slug($nameValue),
            'sort_order' => (int) ($request->sort_order ?? $tag->sort_order),
        ]);

        $tag->translations()->where('key', 'name')->delete();
        $data = [];
        foreach ($langs as $index => $key) {
            $val = trim($names[$index] ?? '');
            if ($val !== '' && $key !== $defaultLang) {
                $data[] = [
                    'translationable_type' => Tag::class,
                    'translationable_id' => $tag->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $val,
                ];
            }
        }
        if (count($data) > 0) {
            $this->translation->insert($data);
        }

        Toastr::success(translate('tag_updated_successfully') ?: 'Tag updated successfully');
        return back();
    }

    public function delete(int $id): RedirectResponse
    {
        $tag = $this->tag->findOrFail($id);
        $tag->delete();
        Toastr::success(translate('tag_deleted_successfully') ?: 'Tag deleted successfully');
        return back();
    }

    public function search(Request $request): JsonResponse
    {
        $q = trim((string) ($request->query('q') ?? ''));
        if (strlen($q) < 1) {
            return response()->json(['tags' => []]);
        }
        $tags = $this->tag->where('name', 'like', '%' . $q . '%')
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);
        return response()->json(['tags' => $tags]);
    }
}
