<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Translation;
use App\Models\UserType;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use UploadSizeHelperTrait;

    public function __construct(
        private Category $category,
        private Translation $translation
    ){
        $this->initUploadLimits();
    }

    /**
     * Build a flattened tree list for category parent dropdowns.
     * Supports multi-level (parent -> child -> grandchild ...).
     *
     * @param int|null $excludeRootId Excludes this category and all its descendants from the list (prevents cycles).
     * @return array<int, array{id:int,label:string,depth:int}>
     */
    private function buildParentCategoryOptions(?int $excludeRootId = null): array
    {
        $all = $this->category
            ->withoutGlobalScopes()
            ->select(['id', 'name', 'parent_id', 'status'])
            ->orderBy('parent_id')
            ->orderBy('name')
            ->get();

        $byParent = [];
        $byId = [];
        foreach ($all as $cat) {
            $byId[(int) $cat->id] = $cat;
            $parentId = (int) ($cat->parent_id ?? 0);
            $byParent[$parentId][] = (int) $cat->id;
        }

        $excluded = [];
        if ($excludeRootId) {
            $excluded = $this->collectDescendantIds($byParent, $excludeRootId, true);
        }

        $options = [];
        $visited = [];
        $dfs = function (int $parentId, int $depth) use (&$dfs, &$options, &$byParent, &$byId, &$visited, $excluded) {
            foreach (($byParent[$parentId] ?? []) as $id) {
                if (isset($visited[$id])) {
                    continue;
                }
                $visited[$id] = true;

                if (in_array($id, $excluded, true)) {
                    continue;
                }

                $cat = $byId[$id] ?? null;
                if (!$cat) {
                    continue;
                }

                $label = str_repeat('— ', $depth) . (string) ($cat->name ?? '');
                $options[] = ['id' => $id, 'label' => $label, 'depth' => $depth];

                $dfs($id, $depth + 1);
            }
        };

        // Start from roots (parent_id=0)
        $dfs(0, 0);

        return $options;
    }

    /**
     * Build nested tree for tree picker UI.
     *
     * @param int|null $excludeRootId Excludes this category and all its descendants (prevents cycles).
     * @return array<int, array{id:int,name:string,children:array}>
     */
    private function buildParentCategoryTree(?int $excludeRootId = null): array
    {
        $all = $this->category
            ->withoutGlobalScopes()
            ->select(['id', 'name', 'parent_id', 'status'])
            ->orderBy('name')
            ->get();

        $byParent = [];
        $byId = [];
        foreach ($all as $cat) {
            $byId[(int) $cat->id] = $cat;
            $parentId = (int) ($cat->parent_id ?? 0);
            $byParent[$parentId][] = (int) $cat->id;
        }

        $excluded = [];
        if ($excludeRootId) {
            $excluded = $this->collectDescendantIds($byParent, $excludeRootId, true);
        }

        $buildNode = function (int $parentId) use (&$buildNode, &$byParent, &$byId, $excluded): array {
            $nodes = [];
            foreach (($byParent[$parentId] ?? []) as $id) {
                if (in_array($id, $excluded, true)) {
                    continue;
                }
                $cat = $byId[$id] ?? null;
                if (!$cat) {
                    continue;
                }
                $nodes[] = [
                    'id' => $id,
                    'name' => (string) ($cat->name ?? ''),
                    'children' => $buildNode($id),
                ];
            }
            return $nodes;
        };

        return $buildNode(0);
    }

    /**
     * Collect descendant IDs (and optionally include the root itself) using an in-memory children map.
     *
     * @param array<int, array<int>> $byParent
     * @param int $rootId
     * @param bool $includeSelf
     * @return array<int>
     */
    private function collectDescendantIds(array $byParent, int $rootId, bool $includeSelf = false): array
    {
        $result = [];
        $stack = [$rootId];
        $seen = [];

        while (!empty($stack)) {
            $current = array_pop($stack);
            if (isset($seen[$current])) {
                continue;
            }
            $seen[$current] = true;

            if ($includeSelf || $current !== $rootId) {
                $result[] = $current;
            }

            foreach (($byParent[$current] ?? []) as $childId) {
                $stack[] = $childId;
            }
        }

        return $result;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): Factory|View|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $search = $request->query('search');
        $status = $request->query('status');
        $featured = $request->query('featured');
        $sortBy = $request->query('sort_by', 'latest');
        $queryParams = ['per_page' => $perPage];

        $query = $this->category->where('parent_id', 0);

        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $queryParams['status'] = $status;
            $query = $query->where('status', (int) $status);
        }

        if ($featured !== null && $featured !== '') {
            $queryParams['featured'] = $featured;
            if ($featured === '1') {
                $query = $query->where('is_featured', 1);
            } elseif ($featured === '0') {
                $query = $query->where('is_featured', 0);
            }
        }

        switch ($sortBy) {
            case 'name_az':
                $query = $query->orderBy('name', 'asc');
                break;
            case 'name_za':
                $query = $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query = $query->latest();
                break;
        }

        if ($sortBy) {
            $queryParams['sort_by'] = $sortBy;
        }

        $categories = $query->paginate($perPage)->appends($queryParams);

        return view('admin-views.category.index', compact('categories', 'search', 'perPage', 'status', 'featured', 'sortBy'));
    }


    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function subIndex(Request $request): View|Factory|Application
    {
        $perPage = (int) $request->query('per_page', Helpers::getPagination());
        $search = $request->query('search');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by', 'latest');
        $queryParams = ['per_page' => $perPage];
        $query = $this->category
            ->with('parent')
            ->where('position', 1);

        if ($search) {
            $queryParams['search'] = $search;
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $queryParams['status'] = $status;
            $query = $query->where('status', (int) $status);
        }

        switch ($sortBy) {
            case 'name_az':
                $query = $query->orderBy('name', 'asc');
                break;
            case 'name_za':
                $query = $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query = $query->latest();
                break;
        }

        if ($sortBy) {
            $queryParams['sort_by'] = $sortBy;
        }

        $categories = $query->paginate($perPage)->appends($queryParams);

        $parentCategoryTree = $this->buildParentCategoryTree();

        return view('admin-views.category.sub-index', compact('categories', 'search', 'perPage', 'status', 'sortBy', 'parentCategoryTree'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function store(Request $request): RedirectResponse|JsonResponse
    {
        $check = $this->validateUploadedFile($request, ['image']);
        if ($check !== true) {
            return $check;
        }

        $position = (int) $request->input('position', 0);
        $rules = [
            'name.0' => 'required|string|max:255',
            'name.*' => 'max:255',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ];
        if ($position !== 0) {
            $rules['parent_id'] = 'required|integer|exists:categories,id';
        } else {
            $rules['parent_id'] = 'nullable|integer';
        }

        $validator = Validator::make($request->all(), $rules, [
            'name.0.required' => $request->parent_id == null
                ? translate('Category name is required!')
                : translate('Sub category name is required!'),
            'name.*.max' => $request->parent_id == null
                ? translate('Category name should not exceed 255 characters')
                : translate('Sub category name should not exceed 255 characters'),
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        foreach ($request->name as $name) {
            $existingCat = $this->category
                ->where('name', $name)
                ->where('parent_id', $request->parent_id ?? 0)
                ->first();

            if ($existingCat) {
                return response()->json([
                    'errors' => [
                        [
                            'code' => 'name.0',
                            'message' => $request->parent_id == null
                                ? translate('Category already exists!')
                                : translate('Sub-category already exists!')
                        ]
                    ]
                ]);
            }
        }


        if (!empty($request->file('image'))) {
            $image_name = Helpers::upload('category/', APPLICATION_IMAGE_FORMAT, $request->file('image'));
        } else {
            $image_name = 'def.png';
        }

        $defaultLocale = config('app.locale', 'ar');
        $defaultIdx = array_search($defaultLocale, $request->lang);
        if ($defaultIdx === false) {
            $defaultIdx = 0;
        }

        $category = $this->category;
        $category->name = $request->name[$defaultIdx];
        $category->image = $image_name;
        $category->banner_image = null;
        $category->parent_id = $request->parent_id == null ? 0 : $request->parent_id;
        $category->position = $position;
        $category->save();

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != $defaultLocale) {
                $data[] = array(
                    'translationable_type' => Category::class,
                    'translationable_id' => $category->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
        }
        if (count($data)) {
            $this->translation->insert($data);
        }

        if ($request->ajax())
        {
            return response()->json([], 200);
        }

        Toastr::success($request->parent_id == 0 ? translate('Category Added Successfully!') : translate('Sub Category Added Successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $category = $this->category->withoutGlobalScopes()->with('translations')->find($id);
        $parentCategoryTree = $this->buildParentCategoryTree((int) $id);
        $userTypes = UserType::orderBy('position')->get();
        return view('admin-views.category.edit', compact('category', 'parentCategoryTree', 'userTypes'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);
        $category->status = $request->status;
        $category->save();
        Toastr::success($category->parent_id == 0 ? translate('Category status updated!') : translate('Sub Category status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse|JsonResponse
    {
        $check = $this->validateUploadedFile($request, ['image']);
        if ($check !== true) {
            return $check;
        }

        $category = $this->category->find($id);
        $isRoot = $category && (int) $category->parent_id === 0;

        $rules = [
            'name.0' => 'required|string|max:255',
            'name.*' => 'max:255',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ];
        if (!$isRoot) {
            $rules['parent_id'] = 'required|integer|exists:categories,id';
        } else {
            $rules['parent_id'] = 'nullable|integer';
        }

        $validator = Validator::make($request->all(), $rules, [
            'name.0.required' => $request->parent_id == null
                ? translate('Category name is required!')
                : translate('Sub category name is required!'),
            'name.*.max' => $request->parent_id == null
                ? translate('Category name should not exceed 255 characters')
                : translate('Sub category name should not exceed 255 characters'),
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        // Prevent cycles: parent cannot be itself or any of its descendants
        if (!$isRoot) {
            $newParentId = (int) $request->input('parent_id');
            if ($newParentId === (int) $id) {
                return response()->json([
                    'errors' => [[
                        'code' => 'parent_id',
                        'message' => translate('Invalid parent category selection.')
                    ]]
                ], 422);
            }

            $all = $this->category
                ->withoutGlobalScopes()
                ->select(['id', 'parent_id'])
                ->get();
            $byParent = [];
            foreach ($all as $c) {
                $byParent[(int) ($c->parent_id ?? 0)][] = (int) $c->id;
            }
            $desc = $this->collectDescendantIds($byParent, (int) $id, false);
            if (in_array($newParentId, $desc, true)) {
                return response()->json([
                    'errors' => [[
                        'code' => 'parent_id',
                        'message' => translate('Invalid parent category selection.')
                    ]]
                ], 422);
            }
        }

        foreach ($request->name as $name) {
            $existingCat = $this->category
                ->where('name', $name)
                ->where('parent_id', $request->parent_id ?? 0)
                ->where('id', '!=', $id)
                ->first();

            if ($existingCat) {
                return response()->json([
                    'errors' => [
                        [
                            'code' => 'name.0',
                            'message' => $request->parent_id == null
                                ? translate('Category already exists!')
                                : translate('Sub category already exists!')
                        ]
                    ]
                ]);
            }
        }

        $defaultLocale = config('app.locale', 'ar');
        $defaultIdx = array_search($defaultLocale, $request->lang);
        if ($defaultIdx === false) {
            $defaultIdx = 0;
        }

        $category->name = $request->name[$defaultIdx];
        $category->parent_id = $request->parent_id == null ? 0 : $request->parent_id;
        $category->image = $request->has('image') ? Helpers::update('category/', $category->image, APPLICATION_IMAGE_FORMAT, $request->file('image')) : $category->image;
        $category->banner_image = null;

        // ── Visibility by user type ──────────────────────────────────────
        // 'visible_everyone' checkbox → NULL (no restriction)
        // Otherwise: array of selected IDs (integers); 0 = guests
        if ($request->boolean('visible_everyone', false)) {
            $category->visible_to_user_types = null;
        } else {
            $rawIds = $request->input('visible_to_user_types', []);
            $category->visible_to_user_types = array_map('intval', (array) $rawIds);
        }

        $category->save();
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && $key != $defaultLocale) {
                $this->translation->updateOrInsert(
                    ['translationable_type' => Category::class,
                        'translationable_id' => $category->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
        }
        if ($request->ajax())
        {
            return response()->json([], 200);
        }
        Toastr::success($category->parent_id == 0 ? translate('Category updated successfully!') : translate('Sub Category updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);

        if ($category->childes->count() == 0) {
            if (Storage::disk('public')->exists('category/' . $category['image'])) {
                Storage::disk('public')->delete('category/' . $category['image']);
            }
            $category->delete();
            Toastr::success($category->parent_id == 0 ? translate('Category removed!') : translate('Sub Category removed!'));
        } else {
            Toastr::warning($category->parent_id == 0 ? translate('Remove subcategories first!') : translate('Sub Remove subcategories first!'));
        }
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function featured(Request $request): RedirectResponse
    {
        $category = $this->category->find($request->id);
        $category->is_featured = $request->featured;
        $category->save();
        Toastr::success(translate('Featured status updated!'));
        return back();
    }
}
