<?php

namespace App\Observers;

use App\Models\Review;
use Illuminate\Support\Facades\Cache;

class ReviewObserver
{
    /**
     * Invalidate dashboard most_rated cache when reviews change.
     */
    private function invalidateMostRatedCache(): void
    {
        Cache::forget('admin_dashboard_most_rated');
    }

    public function created(Review $review): void
    {
        $this->invalidateMostRatedCache();
    }

    public function updated(Review $review): void
    {
        $this->invalidateMostRatedCache();
    }

    public function deleted(Review $review): void
    {
        $this->invalidateMostRatedCache();
    }
}
