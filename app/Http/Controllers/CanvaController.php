<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Design;
use App\Models\ProductTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CanvaController extends Controller
{
    /**
     * Step 1 — Start the design process.
     *
     * • Template has a Canva URL  → render CanvaStart (auto-opens Canva in a new tab,
     *   shows the 3-step workflow page so the customer knows what to do next).
     * • Template has no Canva URL → skip directly to the upload step.
     */
    public function start(ProductTemplate $productTemplate): Response
    {
        abort_if(! $productTemplate->is_active, 404);

        $productTemplate->load('product.subcategory.category');

        // Always render CanvaStart — it handles both cases:
        //  • canvaUrl set   → shows open-canva → edit → upload flow
        //  • canvaUrl null  → jumps straight to the upload step
        return Inertia::render('Storefront/CanvaStart', [
            'template' => $this->present($productTemplate),
            'canvaUrl' => $productTemplate->canva_template_url,
        ]);
    }

    /** Step 2 — Show the file-upload page. */
    public function submitPage(ProductTemplate $productTemplate): Response
    {
        abort_if(! $productTemplate->is_active, 404);

        $productTemplate->load('product.subcategory.category');

        return Inertia::render('Storefront/CanvaSubmit', [
            'template' => $this->present($productTemplate),
        ]);
    }

    /**
     * Step 3 — Accept the uploaded file, create Design + CartItem.
     *
     * Accepts: JPG, PNG, WEBP, PDF — max 20 MB.
     */
    public function submit(Request $request, ProductTemplate $productTemplate): RedirectResponse
    {
        $request->validate([
            'file'     => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:20480',
            'quantity' => 'nullable|integer|min:1|max:999',
        ]);

        $user = $request->user();
        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension()) ?: 'png';
        $path = 'designs/' . Str::uuid() . '.' . $ext;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $productTemplate->load('product');

        $design = Design::create([
            'user_id'             => $user->id,
            'product_template_id' => $productTemplate->id,
            'name'                => $productTemplate->product->name . ' — ' . $productTemplate->name,
            'preview_path'        => $path,
        ]);

        CartItem::create([
            'user_id'             => $user->id,
            'design_id'           => $design->id,
            'product_template_id' => $productTemplate->id,
            'quantity'            => $request->integer('quantity', 1),
        ]);

        return redirect()->route('cart.index')
            ->with('success', 'تمت إضافة تصميمك إلى السلة بنجاح 🎉');
    }

    /* ─── helpers ─── */

    private function present(ProductTemplate $t): array
    {
        return [
            'id'                 => $t->id,
            'name'               => $t->name,
            'preview_image'      => $t->preview_image,
            'canva_template_url' => $t->canva_template_url,
            'product'            => [
                'id'          => $t->product->id,
                'name'        => $t->product->name,
                'slug'        => $t->product->slug,
                'subcategory' => $t->product->subcategory->name,
                'category'    => $t->product->subcategory->category->name,
            ],
        ];
    }
}
