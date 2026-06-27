<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCategoryCacheCommand extends Command
{
    protected $signature = 'elitevape:clear-category-cache';
    protected $description = 'Clear category API cache (run after importing categories/products data)';

    public function handle(): int
    {
        $locales = Translation::distinct()->pluck('locale')->push('ar', 'en')->unique();

        foreach ($locales as $locale) {
            Cache::forget(CACHE_CATEGORY_TABLE . '_' . $locale);
            Cache::forget(CACHE_POPULAR_CATEGORY_TABLE . '_' . $locale);
            Cache::forget(CACHE_FEATURED_CATEGORIES . '_' . $locale);
        }
        Cache::forget(CACHE_CATEGORY_TABLE);
        Cache::forget(CACHE_POPULAR_CATEGORY_TABLE);
        Cache::forget(CACHE_FEATURED_CATEGORIES);

        foreach (Category::distinct()->pluck('parent_id')->filter()->unique() as $parentId) {
            Cache::forget(CACHE_CATEGORY_CHILDREN . '_' . $parentId);
        }

        $this->info('Category cache cleared successfully.');
        return self::SUCCESS;
    }
}
