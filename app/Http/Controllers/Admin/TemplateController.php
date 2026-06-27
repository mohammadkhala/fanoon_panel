<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTemplate;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TemplateController extends Controller
{
    /** List all product templates with their product hierarchy. */
    public function index(): Response
    {
        $templates = ProductTemplate::with(['product.subcategory.category'])
            ->orderBy('product_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ProductTemplate $t) => [
                'id'                  => $t->id,
                'name'                => $t->name,
                'preview_image'       => $t->preview_image,
                'canva_template_url'  => $t->canva_template_url,
                'sort_order'          => $t->sort_order,
                'is_active'           => (bool) $t->is_active,
                'product_id'          => $t->product_id,
                'product_name'        => $t->product?->name,
                'subcategory_name'    => $t->product?->subcategory?->name,
                'category_name'       => $t->product?->subcategory?->category?->name,
            ]);

        return Inertia::render('Admin/Templates/Index', [
            'templates' => $templates,
        ]);
    }

    /** Show create form with cascading selectors. */
    public function create(): Response
    {
        return Inertia::render('Admin/Templates/Form', [
            'template'      => null,
            'categories'    => Category::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'subcategories' => Subcategory::with('category')->orderBy('category_id')->get()
                ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'category_id' => $s->category_id]),
            'products'      => Product::orderBy('sort_order')->get()
                ->map(fn ($p) => [
                    'id'             => $p->id,
                    'name'           => $p->name,
                    'subcategory_id' => $p->subcategory_id,
                    'templates_count'=> $p->templates()->count(),
                ]),
        ]);
    }

    /** Show edit form pre-filled with existing ProductTemplate. */
    public function edit(ProductTemplate $productTemplate): Response
    {
        $productTemplate->load('product.subcategory.category');

        return Inertia::render('Admin/Templates/Form', [
            'template' => [
                'id'                 => $productTemplate->id,
                'name'               => $productTemplate->name,
                'preview_image'      => $productTemplate->preview_image,
                'canva_template_url' => $productTemplate->canva_template_url,
                'sort_order'         => $productTemplate->sort_order,
                'is_active'          => (bool) $productTemplate->is_active,
                'product_id'         => $productTemplate->product_id,
                'subcategory_id'     => $productTemplate->product?->subcategory_id,
                'category_id'        => $productTemplate->product?->subcategory?->category_id,
            ],
            'categories'    => Category::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'subcategories' => Subcategory::with('category')->orderBy('category_id')->get()
                ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'category_id' => $s->category_id]),
            'products'      => Product::orderBy('sort_order')->get()
                ->map(fn ($p) => [
                    'id'             => $p->id,
                    'name'           => $p->name,
                    'subcategory_id' => $p->subcategory_id,
                    'templates_count'=> $p->templates()->count(),
                ]),
        ]);
    }

    /** Create a new ProductTemplate. */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $product = Product::findOrFail($data['product_id']);

        if ($product->templates()->count() >= 3) {
            return back()->withErrors(['product_id' => 'هذا المنتج وصل للحد الأقصى (3 قوالب).']);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('product-templates', 'public');
        }

        ProductTemplate::create([
            'product_id'         => $data['product_id'],
            'name'               => $data['name'],
            'canva_template_url' => $data['canva_template_url'] ?? null,
            'preview_image'      => $imagePath,
            'sort_order'         => $data['sort_order'] ?? ($product->templates()->max('sort_order') + 1),
            'is_active'          => $data['is_active'] ?? true,
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'تمت إضافة القالب.');
    }

    /** Update an existing ProductTemplate. */
    public function update(Request $request, ProductTemplate $productTemplate): RedirectResponse
    {
        $data = $this->validated($request, isEdit: true);

        if ($request->hasFile('image')) {
            if ($productTemplate->preview_image) {
                Storage::disk('public')->delete($productTemplate->preview_image);
            }
            $data['preview_image'] = $request->file('image')->store('product-templates', 'public');
        }

        $productTemplate->update([
            'product_id'         => $data['product_id'],
            'name'               => $data['name'],
            'canva_template_url' => $data['canva_template_url'] ?? null,
            'preview_image'      => $data['preview_image'] ?? $productTemplate->preview_image,
            'sort_order'         => $data['sort_order'],
            'is_active'          => $data['is_active'],
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'تم تحديث القالب.');
    }

    public function toggle(ProductTemplate $productTemplate): RedirectResponse
    {
        $productTemplate->update(['is_active' => !$productTemplate->is_active]);
        return back()->with('success', $productTemplate->is_active ? 'تم تفعيل القالب.' : 'تم إخفاء القالب.');
    }

    public function destroy(ProductTemplate $productTemplate): RedirectResponse
    {
        if ($productTemplate->preview_image) {
            Storage::disk('public')->delete($productTemplate->preview_image);
        }
        $productTemplate->delete();
        return back()->with('success', 'تم حذف القالب.');
    }

    /* ── Validation ── */
    private function validated(Request $request, bool $isEdit = false): array
    {
        return $request->validate([
            'product_id'         => 'required|exists:products,id',
            'name'               => 'required|string|max:100',
            'canva_template_url' => 'nullable|url|max:500',
            'sort_order'         => 'nullable|integer|min:0',
            'is_active'          => 'boolean',
            'image'              => ($isEdit ? 'nullable' : 'nullable') . '|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
    }
}
