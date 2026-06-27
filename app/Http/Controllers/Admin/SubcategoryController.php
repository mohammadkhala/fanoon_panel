<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
        ]);

        Subcategory::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . Str::random(4),
            'icon' => $data['icon'] ?? '📁',
            'description' => $data['description'] ?? null,
            'sort_order' => Subcategory::where('category_id', $data['category_id'])->max('sort_order') + 1,
        ]);

        return back()->with('success', 'تمت إضافة التصنيف الفرعي.');
    }

    public function update(Request $request, Subcategory $subcategory): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $subcategory->update($data);

        return back()->with('success', 'تم تحديث التصنيف الفرعي.');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $subcategory->delete();

        return back()->with('success', 'تم حذف التصنيف الفرعي.');
    }
}
