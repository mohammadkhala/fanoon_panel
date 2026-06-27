<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Design;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    /**
     * Save the customer's edited design and add it straight to the cart.
     * Templates can't be bought as-is — they must pass through here first.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'template_id' => 'nullable|exists:templates,id',
            'name' => 'nullable|string|max:255',
            'fabric_json' => 'required|array',
            'preview' => 'nullable|string', // data:image/png;base64,....
            'quantity' => 'nullable|integer|min:1|max:999',
        ]);

        $user = $request->user();
        $template = isset($data['template_id']) ? Template::find($data['template_id']) : null;

        $previewPath = $this->storePreview($data['preview'] ?? null);

        $design = Design::create([
            'user_id' => $user->id,
            'template_id' => $template?->id,
            'name' => $data['name'] ?? $template?->name ?? 'تصميم مخصّص',
            'fabric_json' => $data['fabric_json'],
            'preview_path' => $previewPath,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'design_id' => $design->id,
            'template_id' => $template?->id,
            'quantity' => $data['quantity'] ?? 1,
        ]);

        return redirect()->route('cart.index')
            ->with('success', 'تمت إضافة تصميمك إلى السلة بنجاح.');
    }

    private function storePreview(?string $dataUrl): ?string
    {
        if (! $dataUrl || ! Str::startsWith($dataUrl, 'data:image')) {
            return null;
        }

        [, $encoded] = explode(',', $dataUrl, 2);
        $binary = base64_decode($encoded);
        if ($binary === false) {
            return null;
        }

        $path = 'designs/' . Str::uuid() . '.png';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
