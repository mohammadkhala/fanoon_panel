<?php

namespace App\CentralLogics;


use App\Models\FlashSale;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProductLogic
{
    public static function get_product($id)
    {
        return Product::active()->withCount(['wishlist'])->with(['rating', 'reviews'])->where('id', $id)->first();
    }

    public static function get_latest_products($sort_by, $limit = 10, $offset = 1)
    {
        $limit = Helpers::capApiLimit($limit);
        $offset = Helpers::capApiOffset($offset);

        $paginator = Product::active()
            ->withCount(['wishlist'])
            ->with(['rating'])
            ->when($sort_by == 'price_high_to_low', function ($query) {
                return $query->orderBy('price', 'desc');
            })
            ->when($sort_by == 'price_low_to_high', function ($query) {
                return $query->orderBy('price', 'asc');
            })
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_related_products($product_id)
    {
        $product = Product::find($product_id);
        return Product::active()->withCount(['wishlist'])->with(['rating'])->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();
    }

    /**
     * منتجات اشتراها العملاء مع هذا المنتج (من نفس الطلب).
     */
    public static function get_customers_also_bought($product_id, int $limit = 10)
    {
        $limit = Helpers::capApiLimit($limit);
        $orderIds = \App\Models\OrderDetail::where('product_id', $product_id)
            ->whereHas('order', fn ($q) => $q->where('order_status', 'delivered'))
            ->pluck('order_id');

        if ($orderIds->isEmpty()) {
            return collect();
        }

        $otherProductIds = \App\Models\OrderDetail::whereIn('order_id', $orderIds)
            ->where('product_id', '!=', $product_id)
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->pluck('product_id');

        if ($otherProductIds->isEmpty()) {
            return collect();
        }

        $ids = $otherProductIds->implode(',');
        return Product::active()
            ->withCount(['wishlist'])
            ->with(['rating'])
            ->whereIn('id', $otherProductIds)
            ->orderByRaw("FIELD(id, {$ids})")
            ->get();
    }

    public static function search_products($name, $price_low, $price_high, $rating, $category_ids, $sort_by, $limit = 10, $offset = 1, $in_stock_only = false, $tag_ids = null, $attribute_ids = null)
    {
        $limit = Helpers::capApiLimit($limit);
        $offset = Helpers::capApiOffset($offset);
        $key = $name;

        $searched_products = Product::active();

        // Clone query for price range
        $priceRangeQuery = clone $searched_products;
        $lowest_price = $priceRangeQuery->min('price');
        $highest_price = $priceRangeQuery->max('price');
        $searched_products = $searched_products->withCount('wishlist')
            ->with('rating')
            ->when($key, function ($query) use ($key) {
                $query->where(function ($q) use ($key) {
                    $q->orWhere('name', 'like', "%{$key}%");
                });
            })
            ->when($price_low !== null && $price_high !== null, function ($query) use ($price_low, $price_high) {
                return $query->whereBetween('price', [$price_low, $price_high]);
            })
            ->when($category_ids, function ($query) use ($category_ids) {
                $categories = is_array($category_ids) ? $category_ids : json_decode($category_ids, true);
                $query->where(function ($q) use ($categories) {
                    foreach ($categories as $categoryId) {
                        $q->orWhereJsonContains('category_ids', ['id' => (string)$categoryId]);
                    }
                });
            })
            ->when($rating !== null, function ($query) use ($rating) {
                $query->whereHas('reviews', function ($q) use ($rating) {
                    $q->select('product_id')
                        ->groupBy('product_id')
                        ->havingRaw('AVG(rating) >= ?', [$rating]);
                });
            })
            ->when($in_stock_only, fn ($query) => $query->where('total_stock', '>', 0))
            ->when($tag_ids, function ($query) use ($tag_ids) {
                $ids = is_array($tag_ids) ? array_map('intval', $tag_ids) : array_map('intval', array_filter(explode(',', (string) $tag_ids)));
                if (!empty($ids)) {
                    $query->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $ids));
                }
            })
            ->when($attribute_ids, function ($query) use ($attribute_ids) {
                $ids = is_array($attribute_ids) ? array_map('intval', $attribute_ids) : array_map('intval', array_filter(explode(',', (string) $attribute_ids)));
                if (!empty($ids)) {
                    $query->whereNotNull('attributes')
                        ->where('attributes', '!=', '')
                        ->where('attributes', '!=', '[]')
                        ->where(function ($q) use ($ids) {
                            foreach ($ids as $attrId) {
                                if (DB::getDriverName() === 'sqlite') {
                                    $q->orWhere(function ($inner) use ($attrId) {
                                        $inner->where('attributes', '=', '[' . $attrId . ']')
                                            ->orWhere('attributes', 'like', '[' . $attrId . ',%')
                                            ->orWhere('attributes', 'like', '%,' . $attrId . ',%')
                                            ->orWhere('attributes', 'like', '%,' . $attrId . ']');
                                    });
                                } else {
                                    $q->orWhereJsonContains('attributes', $attrId);
                                }
                            }
                        });
                }
            })
            ->when($sort_by, function ($query) use ($sort_by) {
                switch ($sort_by) {
                    case 'new_arrival':
                        $query->where('created_at', '>=', now()->subMonths(3))
                            ->orderBy('created_at', 'desc');
                        break;

                    case 'offer_product':
                        $query->where('discount', '>', 0)
                            ->orderBy('discount', 'desc');
                        break;

                    case 'low_high':
                    case 'price_low_to_high':
                        $query->orderBy('price', 'asc');
                        break;

                    case 'high_low':
                    case 'price_high_to_low':
                        $query->orderBy('price', 'desc');
                        break;

                    case 'a_to_z':
                        $query->orderBy('name', 'asc');
                        break;

                    case 'z_to_a':
                        $query->orderBy('name', 'desc');
                        break;

                    case 'top_rated':
                        $query->withAvg('reviews', 'rating')
                            ->orderByDesc('reviews_avg_rating');
                        break;

                    case 'best_selling':
                        $query->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                            ->leftJoin('orders', function ($join) {
                                $join->on('order_details.order_id', '=', 'orders.id')
                                    ->where('orders.order_status', '=', 'delivered');
                            })
                            ->selectRaw('products.*, COALESCE(SUM(order_details.quantity), 0) as sold_count')
                            ->groupBy('products.id')
                            ->orderByDesc('sold_count');
                        break;

                    default:
                        $query->latest(); // fallback sort
                        break;
                }
            });



        $paginator = $searched_products->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'lowest_price' => (int)($lowest_price ?? 0),
            'highest_price' => (int)($highest_price ?? 0),
            'price_high' => $price_high,
            'price_low' => $price_low,
            'rating' => $rating,
            'category_ids' => $category_ids,
            'tag_ids' => $tag_ids,
            'sort_by' => $sort_by,
            'products' => $paginator->items(),
        ];
    }

    public static function filterFlashSale($flashSaleProductIds, $price_low, $price_high, $rating, $category_ids, $sort_by, $limit = 10, $offset = 1, $tag_ids = null, $in_stock_only = false, $attribute_ids = null)
    {
        $limit = Helpers::capApiLimit($limit);
        $offset = Helpers::capApiOffset($offset);
        $query = Product::active()
            ->whereIn('id', $flashSaleProductIds)
            ->withCount('wishlist')
            ->with('rating')
            ->when($in_stock_only, fn ($q) => $q->where('total_stock', '>', 0));

        // Get min & max price BEFORE pagination (clone query)
        $priceRangeQuery = (clone $query);
        $lowest_price = $priceRangeQuery->min('price');
        $highest_price = $priceRangeQuery->max('price');

        // Filter by rating
        if (!empty($rating)) {
            $query->whereHas('reviews', function ($q) use ($rating) {
                $q->select('product_id')
                    ->groupBy('product_id')
                    ->havingRaw('AVG(rating) >= ?', [$rating]);
            });
        }

        // Filter by category (supports multiple)
        if (!empty($category_ids)) {
            $categoryIds = is_array($category_ids) ? $category_ids : json_decode($category_ids, true);
            $query->where(function ($q) use ($categoryIds) {
                foreach ($categoryIds as $categoryId) {
                    $q->orWhereJsonContains('category_ids', ['id' => (string)$categoryId]);
                }
            });
        }

        // Filter by tags
        if (!empty($tag_ids)) {
            $ids = is_array($tag_ids) ? array_map('intval', $tag_ids) : array_map('intval', array_filter(explode(',', (string) $tag_ids)));
            if (!empty($ids)) {
                $query->whereHas('tags', fn ($q) => $q->whereIn('tags.id', $ids));
            }
        }

        // Filter by attributes
        if (!empty($attribute_ids)) {
            $ids = is_array($attribute_ids) ? array_map('intval', $attribute_ids) : array_map('intval', array_filter(explode(',', (string) $attribute_ids)));
            if (!empty($ids)) {
                $query->whereNotNull('attributes')
                    ->where('attributes', '!=', '')
                    ->where('attributes', '!=', '[]')
                    ->where(function ($q) use ($ids) {
                        foreach ($ids as $attrId) {
                            if (DB::getDriverName() === 'sqlite') {
                                $q->orWhere(function ($inner) use ($attrId) {
                                    $inner->where('attributes', '=', '[' . $attrId . ']')
                                        ->orWhere('attributes', 'like', '[' . $attrId . ',%')
                                        ->orWhere('attributes', 'like', '%,' . $attrId . ',%')
                                        ->orWhere('attributes', 'like', '%,' . $attrId . ']');
                                });
                            } else {
                                $q->orWhereJsonContains('attributes', $attrId);
                            }
                        }
                    });
            }
        }

        // Filter by price range
        if ($price_low !== null && $price_high !== null) {
            $query->whereBetween('price', [$price_low, $price_high]);
        }

        // Sorting
        switch ($sort_by) {
            case 'new_arrival':
                $query->where('created_at', '>=', now()->subMonths(3))->orderBy('created_at', 'desc');
                break;
            case 'offer_product':
                $query->where('discount', '>', 0)->orderBy('discount', 'desc');
                break;
            case 'price_high_to_low':
                $query->orderBy('price', 'desc');
                break;
            case 'price_low_to_high':
                $query->orderBy('price', 'asc');
                break;
            case 'a_to_z':
                $query->orderBy('name', 'asc');
                break;
            case 'z_to_a':
                $query->orderBy('name', 'desc');
                break;
            case 'top_rated':
                $query->withAvg('reviews', 'rating')
                    ->orderByDesc('reviews_avg_rating');
                break;
            case 'best_selling':
                $query->leftJoin('order_details', 'products.id', '=', 'order_details.product_id')
                    ->leftJoin('orders', function ($join) {
                        $join->on('order_details.order_id', '=', 'orders.id')
                            ->where('orders.order_status', '=', 'delivered');
                    })
                    ->selectRaw('products.*, COALESCE(SUM(order_details.quantity), 0) as sold_count')
                    ->groupBy('products.id')
                    ->orderByDesc('sold_count');
                break;
        }



        // Apply pagination
        $paginator = $query->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'lowest_price' => (int)($lowest_price ?? 0),
            'highest_price' => (int)($highest_price ?? 0),
            'price_high' => $price_high,
            'price_low' => $price_low,
            'rating' => $rating,
            'sort_by' => $sort_by,
            'category_ids' => $category_ids,
            'tag_ids' => $tag_ids,
            'flash_sale' => FlashSale::active()->first(),
            'products' => $paginator->items(),
        ];
    }

    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function get_favorite_products($limit, $offset, $user_id)
    {
        $limit = Helpers::capApiLimit($limit);
        $offset = Helpers::capApiOffset($offset);

        $ids = User::with('wishlist_products')->find($user_id)->wishlist_products->pluck('product_id')->toArray();
        $wishlist_products = Product::whereIn('id', $ids)->paginate($limit, ['*'], 'page', $offset);

        $formatted_products = Helpers::product_data_formatting($wishlist_products, true);

        return [
            'total_size' => $wishlist_products->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $formatted_products
        ];
    }

    public static function get_new_arrival_products($limit = 10, $offset = 1)
    {
        $limit = Helpers::capApiLimit($limit);
        $offset = Helpers::capApiOffset($offset);
        $threeMonthsAgo = now()->subMonths(3);

        $paginator = Product::active()
            ->withCount(['wishlist'])
            ->with(['rating'])
            ->where('created_at', '>=', $threeMonthsAgo)
            ->latest()
            ->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_product_rating_reviews($product)
    {
        $totalReview = $product->reviews()->count();

        // Average rating
        $averageRating = $product->reviews()
            ->avg('rating');
        $averageRating = round($averageRating, 2);
        $ratingGroupCount = $product->reviews()->select('rating', DB::raw('count(rating) as total'))
            ->groupBy('rating')
            ->orderBy('rating', 'asc')
            ->pluck('total', 'rating');
        // Count of each rating type
        $ratings = [1, 2, 3, 4, 5];  // List of possible ratings (1 to 5)
        $ratingData = [];

        foreach ($ratings as $rating) {
            // If the rating exists in the results, use its count, otherwise set to 0
            $ratingData[$rating] = $ratingGroupCount->get($rating, 0);
        }

        return [
            'total_review' => $totalReview,
            'average_rating' => $averageRating,
            'rating_group_count' => $ratingData,
        ];

    }
}
