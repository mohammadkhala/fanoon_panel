<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function created(Category $category)
    {
        $this->refreshCategoryCache($category);
    }

    /**
     * Handle the Category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        $this->refreshCategoryCache($category);
    }

    /**
     * Handle the Category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
        $this->refreshCategoryCache($category);
    }

    /**
     * Handle the Category "restored" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        $this->refreshCategoryCache($category);
    }

    /**
     * Handle the Category "force deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        $this->refreshCategoryCache($category);
    }

    private function refreshCategoryCache(?Category $category = null): void
    {
        // Clear legacy (non-locale) keys
        Cache::forget(CACHE_CATEGORY_TABLE);
        Cache::forget(CACHE_POPULAR_CATEGORY_TABLE);

        // Clear locale-specific category caches so all languages get fresh data
        $locales = Translation::distinct()->pluck('locale')->push('en')->unique();
        foreach ($locales as $locale) {
            Cache::forget(CACHE_CATEGORY_TABLE . '_' . $locale);
            Cache::forget(CACHE_POPULAR_CATEGORY_TABLE . '_' . $locale);
            Cache::forget(CACHE_FEATURED_CATEGORIES . '_' . $locale);
        }
        Cache::forget(CACHE_FEATURED_CATEGORIES);

        // Clear children cache: affected parents + all from DB
        $parentIds = collect(Category::distinct()->pluck('parent_id')->filter()->unique());
        if ($category) {
            $parentIds = $parentIds->push($category->parent_id);
            if (method_exists($category, 'getOriginal') && $category->getOriginal('parent_id')) {
                $parentIds = $parentIds->push($category->getOriginal('parent_id'));
            }
            $parentIds = $parentIds->filter()->unique();
        }
        foreach ($parentIds as $parentId) {
            Cache::forget(CACHE_CATEGORY_CHILDREN . '_' . $parentId);
        }
    }

}
