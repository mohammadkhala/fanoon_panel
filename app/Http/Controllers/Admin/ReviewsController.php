<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Throwable;

class ReviewsController extends Controller
{
    public function __construct(
        private Review $review
    ){}

    /**
     * Business purpose: provide a filterable reviews list for admins
     * so they can quickly inspect product feedback quality over time.
     */
    public function list(Request $request): View|Factory|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');
        $rating = $request->query('rating');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Start query
        $query = $this->review;

        // Apply search filter
        if ($search) {
            $queryParams['search'] = $search;

            $query = $query->whereHas('product', function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Apply rating filter
        if (!is_null($rating) && $rating !== '') {
            $queryParams['rating'] = $rating;
            $query = $query->where('rating', (int)$rating);
        }

        // Apply date range filter
        if (!is_null($startDate) && $startDate !== '') {
            $queryParams['start_date'] = $startDate;
            $query = $query->whereDate('created_at', '>=', $startDate);
        }

        if (!is_null($endDate) && $endDate !== '') {
            $queryParams['end_date'] = $endDate;
            $query = $query->whereDate('created_at', '<=', $endDate);
        }

        $reviews = $query->with(['product', 'customer'])->latest()->paginate($perPage)->appends($queryParams);

        return view('admin-views.reviews.list', compact(
            'reviews',
            'search',
            'rating',
            'startDate',
            'endDate',
            'perPage'
        ));
    }

    /**
     * Business purpose: allow admin to remove invalid or abusive
     * review records from the moderation list.
     */
    public function delete(Request $request, int $id): RedirectResponse
    {
        try {
            $review = $this->review->find($id);
            if (!$review) {
                Toastr::warning(translate('Review not found!'));
                return back();
            }

            $review->delete();
            Toastr::success(translate('Review removed successfully!'));
            return back();
        } catch (Throwable $exception) {
            report($exception);
            Toastr::error(translate('Unable to remove review right now!'));
            return back();
        }
    }

}
