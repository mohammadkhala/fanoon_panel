<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EditorController extends Controller
{
    /** Open the editor for a template, or to edit an existing design. */
    public function index(Request $request): Response
    {
        $template = null;
        $design = null;

        if ($request->filled('design')) {
            $design = Design::where('user_id', $request->user()?->id)
                ->findOrFail($request->query('design'));
            $template = $design->template;
        } elseif ($request->filled('template')) {
            $template = Template::findOrFail($request->query('template'));
        } else {
            $template = Template::where('is_active', true)->orderBy('sort_order')->first();
        }

        return Inertia::render('Editor/Index', [
            'template' => $template ? [
                'id' => $template->id,
                'name' => $template->name,
                'preview_image' => $template->preview_image,
                'fabric_json' => $template->fabric_json,
            ] : null,
            'design' => $design ? [
                'id' => $design->id,
                'name' => $design->name,
                'fabric_json' => $design->fabric_json,
            ] : null,
            'templates' => Template::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'preview_image']),
        ]);
    }
}
