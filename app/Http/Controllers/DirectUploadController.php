<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Design;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DirectUploadController extends Controller
{
    /** Show the direct-upload page for a product. */
    public function page(Request $request, Product $product): Response
    {
        abort_unless($product->is_active, 404);

        $product->load('subcategory.category');
        $user = $request->user();

        return Inertia::render('Storefront/DirectUpload', [
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'description' => $product->description,
                'price'       => $product->priceFor($user),
                'cover_image' => $product->cover_image,
                'sizes'       => $product->sizes ?? [],
                'subcategory' => [
                    'name' => $product->subcategory->name,
                    'slug' => $product->subcategory->slug,
                ],
                'category' => [
                    'name' => $product->subcategory->category->name,
                    'slug' => $product->subcategory->category->slug,
                ],
            ],
        ]);
    }

    /** Store the uploaded design and add to cart. */
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $request->validate([
            'file'     => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:20480',
            'quantity' => 'nullable|integer|min:1|max:999',
            'notes'    => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension()) ?: 'png';
        $path = 'designs/' . Str::uuid() . '.' . $ext;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        $design = Design::create([
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'name'       => $product->name . ' — تصميم خاص',
            'preview_path' => $path,
        ]);

        CartItem::create([
            'user_id'    => $user->id,
            'design_id'  => $design->id,
            'product_id' => $product->id,
            'quantity'   => $request->integer('quantity', 1),
        ]);

        return redirect()->route('cart.index')
            ->with('success', 'تمت إضافة تصميمك إلى السلة بنجاح 🎉');
    }
}
