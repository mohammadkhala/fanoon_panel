<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Design;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DesignController extends Controller
{
    /** List all customer designs for admin review. */
    public function index(): Response
    {
        $designs = Design::with(['user', 'productTemplate.product.subcategory.category'])
            ->latest()
            ->get()
            ->map(fn (Design $d) => [
                'id'           => $d->id,
                'name'         => $d->name ?? 'بدون اسم',
                'preview_path' => $d->preview_path,
                'created_at'   => $d->created_at->format('Y-m-d'),
                'user_name'    => $d->user?->name ?? '—',
                'user_email'   => $d->user?->email ?? '—',
                'product_name' => $d->productTemplate?->product?->name,
                'category_name'=> $d->productTemplate?->product?->subcategory?->category?->name,
                'already_promoted' => false, // could add logic later
            ]);

        return Inertia::render('Admin/Designs/Index', [
            'designs'       => $designs,
            'categories'    => Category::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'subcategories' => Subcategory::with('category')->orderBy('category_id')->get()
                ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'category_id' => $s->category_id]),
            'products'      => Product::with('subcategory')
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($p) => [
                    'id'             => $p->id,
                    'name'           => $p->name,
                    'subcategory_id' => $p->subcategory_id,
                    'templates_count'=> $p->templates()->count(),
                ]),
        ]);
    }

    /**
     * Promote a customer design to a product template.
     * Accepts: product_id, name (optional).
     */
    public function promoteToTemplate(Request $request, Design $design): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'name'       => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->templates()->count() >= 3) {
            return back()->with('error', 'المنتج "' . $product->name . '" وصل للحد الأقصى (3 قوالب).');
        }

        // Copy preview image to product-templates folder
        $newPath = null;
        if ($design->preview_path && Storage::disk('public')->exists($design->preview_path)) {
            $ext     = pathinfo($design->preview_path, PATHINFO_EXTENSION) ?: 'png';
            $newPath = 'product-templates/' . Str::random(24) . '.' . $ext;
            Storage::disk('public')->copy($design->preview_path, $newPath);
        }

        $templateName = $data['name'] ?: ('تصميم معتمد — ' . ($design->user?->name ?? '') . ' ' . now()->format('d/m/Y'));

        $product->templates()->create([
            'name'          => $templateName,
            'preview_image' => $newPath,
            'sort_order'    => ($product->templates()->max('sort_order') ?? 0) + 1,
            'is_active'     => true,
        ]);

        return back()->with('success',
            'تمت إضافة تصميم "' . $design->name . '" كقالب للمنتج "' . $product->name . '".'
        );
    }
}
