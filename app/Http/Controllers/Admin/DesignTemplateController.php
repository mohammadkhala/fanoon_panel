<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DesignTemplate;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignTemplateController extends Controller
{
    private function categories(): array
    {
        $main = Category::where('parent_id', 0)->select('id', 'name')->orderBy('name')->get();
        $sub  = Category::where('parent_id', '!=', 0)->select('id', 'name', 'parent_id')->orderBy('parent_id')->orderBy('name')->get();
        return [$main, $sub];
    }

    private function products()
    {
        return Product::withoutGlobalScopes()->select('id', 'name')->orderBy('name')->limit(600)->get();
    }

    public function byCategory(Request $request)
    {
        $search     = $request->input('search', '');
        $productId  = $request->input('product_id');
        $categoryId = $request->input('category_id');
        $status     = $request->input('status', '');   // '' | '1' | '0'

        // فلاتر تعمل على أعمدة القالب نفسه (التصنيف نتعامل معه لاحقاً مع الاشتقاق من المنتج)
        $query = DesignTemplate::with(['mainCategory.parent', 'product'])
            ->orderBy('position')
            ->orderBy('id', 'desc');

        if ($search)        $query->where('name', 'like', "%{$search}%");
        if ($productId)     $query->where('product_id', $productId);
        if ($status !== '') $query->where('status', (int) $status);

        $all = $query->get();

        // خريطة التصنيفات (id => [name, parent_id]) لاشتقاق تصنيف المنتج بدون استعلامات متكررة
        $catMap = Category::get(['id', 'name', 'parent_id'])->keyBy('id');

        // لكل قالب: احسب التصنيف الفعّال (الخاص به، وإلا المشتق من المنتج المرتبط)
        $all->each(function ($t) use ($catMap) {
            $t->setAttribute('effective_cat', $this->effectiveCategory($t, $catMap));
        });

        // فلترة حسب التصنيف على القيمة الفعّالة (رئيسي أو فرعي)
        if ($categoryId) {
            $all = $all->filter(function ($t) use ($categoryId) {
                $ec = $t->effective_cat;
                return (string) $ec['main_id'] === (string) $categoryId
                    || (string) $ec['sub_id']  === (string) $categoryId;
            })->values();
        }

        // التجميع حسب التسمية: "بدون تصنيف" | "الرئيسي" | "الرئيسي › الفرعي"
        $grouped = $all->groupBy(fn ($t) => $t->effective_cat['label'])->sortKeys();

        // Move "بدون تصنيف" to the end
        if ($grouped->has('__none__')) {
            $none = $grouped->pull('__none__');
            $grouped->put('بدون تصنيف', $none);
        }

        $filterProduct  = $productId ? Product::select('id', 'name')->find($productId) : null;
        $allProducts    = $this->products();
        [$mainCategories] = $this->categories();

        return view('admin-views.design-template.by-category',
            compact('grouped', 'search', 'productId', 'categoryId', 'status', 'filterProduct', 'allProducts', 'mainCategories'));
    }

    /**
     * يحسب التصنيف الفعّال للقالب: تصنيفه الخاص إن وُجد، وإلا المشتق من تصنيف المنتج المرتبط.
     * يرجع ['main_id', 'sub_id', 'label'].
     */
    private function effectiveCategory(DesignTemplate $t, $catMap): array
    {
        // 1) تصنيف القالب نفسه
        if ($t->category_id && $cat = $catMap->get($t->category_id)) {
            if ((int) $cat->parent_id === 0) {
                return ['main_id' => $cat->id, 'sub_id' => null, 'label' => $cat->name];
            }
            $parent = $catMap->get($cat->parent_id);
            return [
                'main_id' => $cat->parent_id,
                'sub_id'  => $cat->id,
                'label'   => ($parent->name ?? '—') . ' › ' . $cat->name,
            ];
        }

        // 2) الاشتقاق من تصنيف المنتج المرتبط (category_ids: [{id, position}, ...])
        if ($t->product) {
            $raw = $t->product->getAttributes()['category_ids'] ?? null;
            $ids = is_array($raw) ? $raw : json_decode((string) $raw, true);
            if (is_array($ids) && count($ids)) {
                $collection = collect($ids)->sortBy('position')->values();
                $mainId = $collection->firstWhere('position', 1)['id'] ?? ($collection->first()['id'] ?? null);
                $subId  = $collection->firstWhere('position', 2)['id'] ?? null;

                $mainCat = $mainId ? $catMap->get($mainId) : null;
                $subCat  = $subId  ? $catMap->get($subId)  : null;

                if ($mainCat) {
                    return [
                        'main_id' => $mainCat->id,
                        'sub_id'  => $subCat?->id,
                        'label'   => $subCat ? ($mainCat->name . ' › ' . $subCat->name) : $mainCat->name,
                    ];
                }
            }
        }

        return ['main_id' => null, 'sub_id' => null, 'label' => '__none__'];
    }

    public function index(Request $request)
    {
        $search      = $request->input('search', '');
        $perPage     = (int) $request->input('per_page', 20);
        $fromProductId = $request->input('product_id');

        $query = DesignTemplate::with('mainCategory')->orderBy('position')->orderBy('id', 'desc');
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $templates = $query->paginate($perPage)->withQueryString();
        [$mainCategories, $subCategories] = $this->categories();

        $products      = $this->products();
        $fromProduct   = $fromProductId ? Product::select('id', 'name')->find($fromProductId) : null;
        $prefill       = null;

        return view('admin-views.design-template.index',
            compact('templates', 'search', 'perPage', 'mainCategories', 'subCategories', 'products', 'fromProduct', 'fromProductId', 'prefill'));
    }

    /**
     * يفتح محرّر القوالب محمّلاً بتصميم زبون من تفصيل طلب، ليعدّله الأدمن ثم يحفظه كقالب.
     */
    public function createFromOrderDetail($detailId)
    {
        $detail = OrderDetail::findOrFail($detailId);

        if (empty($detail->design_json)) {
            return redirect()
                ->route('admin.orders.details', $detail->order_id)
                ->with('error', 'هذا التصميم لا يحتوي على بيانات قابلة للتعديل.');
        }

        $search        = '';
        $perPage       = 20;
        $fromProductId = $detail->product_id;

        $templates = DesignTemplate::with('mainCategory')
            ->orderBy('position')->orderBy('id', 'desc')
            ->paginate($perPage);

        [$mainCategories, $subCategories] = $this->categories();
        $products    = $this->products();
        $fromProduct = $fromProductId ? Product::select('id', 'name')->find($fromProductId) : null;

        $prefill = [
            'json'       => $detail->design_json,
            'width'      => (int) ($detail->design_width ?: 800),
            'height'     => (int) ($detail->design_height ?: 800),
            'product_id' => $detail->product_id,
            'name'       => 'قالب من طلب #' . $detail->order_id,
        ];

        return view('admin-views.design-template.index',
            compact('templates', 'search', 'perPage', 'mainCategories', 'subCategories', 'products', 'fromProduct', 'fromProductId', 'prefill'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'canvas_json'=> 'required|string',
        ]);

        $template               = new DesignTemplate();
        $template->name         = $request->name;
        $template->category_id  = $request->input('category_id') ?: null;
        $template->product_id   = $request->input('product_id') ?: null;
        $template->canvas_json  = $request->canvas_json;
        $template->canvas_width = max(100, (int) $request->input('canvas_width', 800));
        $template->canvas_height= max(100, (int) $request->input('canvas_height', 800));
        $template->position     = (int) $request->input('position', 0);
        $template->status       = 1;
        $template->thumbnail    = $this->saveThumbnail($request->input('thumbnail_base64'));
        $template->save();

        Cache::forget(CACHE_DESIGN_TEMPLATES);

        return redirect()->route('admin.product.list')
            ->with('success', translate('template_added') ?: 'تم إضافة القالب بنجاح');
    }

    public function edit($id)
    {
        $template = DesignTemplate::findOrFail($id);
        [$mainCategories, $subCategories] = $this->categories();

        // Pre-select main category
        $selectedMain = null;
        $selectedSub  = null;
        if ($template->category_id) {
            $cat = Category::find($template->category_id);
            if ($cat) {
                if ($cat->parent_id == 0) {
                    $selectedMain = $cat->id;
                } else {
                    $selectedMain = $cat->parent_id;
                    $selectedSub  = $cat->id;
                }
            }
        }

        $products = $this->products();

        return view('admin-views.design-template.edit',
            compact('template', 'mainCategories', 'subCategories', 'selectedMain', 'selectedSub', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'product_id' => 'required|exists:products,id',
            'canvas_json'=> 'required|string',
        ]);

        $template               = DesignTemplate::findOrFail($id);
        $template->name         = $request->name;
        $template->category_id  = $request->input('category_id') ?: null;
        $template->product_id   = $request->input('product_id') ?: null;
        $template->canvas_json  = $request->canvas_json;
        $template->canvas_width = max(100, (int) $request->input('canvas_width', $template->canvas_width));
        $template->canvas_height= max(100, (int) $request->input('canvas_height', $template->canvas_height));
        $template->position     = (int) $request->input('position', $template->position);

        if ($request->filled('thumbnail_base64')) {
            if ($template->thumbnail) {
                Storage::disk('public')->delete('design-templates/' . $template->thumbnail);
            }
            $template->thumbnail = $this->saveThumbnail($request->input('thumbnail_base64'));
        }

        $template->save();
        Cache::forget(CACHE_DESIGN_TEMPLATES);

        return redirect()->route('admin.design-template.edit', $id)
            ->with('success', translate('template_updated') ?: 'تم تحديث القالب بنجاح');
    }

    public function status($id, $status)
    {
        $template = DesignTemplate::findOrFail($id);
        $template->update(['status' => (int) $status]);
        Cache::forget(CACHE_DESIGN_TEMPLATES);
        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $template = DesignTemplate::findOrFail($id);
        if ($template->thumbnail) {
            Storage::disk('public')->delete('design-templates/' . $template->thumbnail);
        }
        $template->delete();
        Cache::forget(CACHE_DESIGN_TEMPLATES);

        return redirect()->route('admin.design-template.add-new')
            ->with('success', translate('template_deleted') ?: 'تم حذف القالب');
    }

    private function saveThumbnail(?string $base64): ?string
    {
        if (!$base64 || !str_starts_with($base64, 'data:image')) {
            return null;
        }
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        if (!$data || strlen($data) > 3 * 1024 * 1024) {
            return null;
        }
        $filename = Str::uuid() . '.png';
        Storage::disk('public')->makeDirectory('design-templates');
        Storage::disk('public')->put('design-templates/' . $filename, $data);
        return $filename;
    }
}
