<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter'); // approved | pending

        $query = Review::with(['user', 'template'])->latest();
        if ($filter === 'approved') {
            $query->where('is_approved', true);
        } elseif ($filter === 'pending') {
            $query->where('is_approved', false);
        }

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $query->get()->map(fn ($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'is_approved' => $r->is_approved,
                'created_at' => $r->created_at->format('Y-m-d'),
                'customer' => $r->user?->name,
                'template' => $r->template?->name,
            ]),
            'filters' => ['filter' => $filter],
            'counts' => [
                'all' => Review::count(),
                'approved' => Review::where('is_approved', true)->count(),
                'pending' => Review::where('is_approved', false)->count(),
            ],
        ]);
    }

    public function toggle(Review $review)
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back();
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'تم حذف التقييم.');
    }
}
