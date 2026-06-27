<?php

namespace App\CentralLogics;

use App\Models\Category;
use App\Models\Product;

class CategoryLogic
{
    /**
     * Collect this category id + all descendants (any depth).
     *
     * @param int $id
     * @return array<int>
     */
    private static function selfAndDescendantIds(int $id): array
    {
        $cats = Category::select(['id', 'parent_id'])->get();

        $byParent = [];
        foreach ($cats as $c) {
            $byParent[(int) ($c->parent_id ?? 0)][] = (int) $c->id;
        }

        $ids = [];
        $stack = [$id];
        $seen = [];
        while (!empty($stack)) {
            $current = array_pop($stack);
            if (isset($seen[$current])) {
                continue;
            }
            $seen[$current] = true;
            $ids[] = $current;
            foreach (($byParent[$current] ?? []) as $childId) {
                $stack[] = $childId;
            }
        }

        return $ids;
    }

    /**
     * @return mixed
     */
    public static function parents(): mixed
    {
        return Category::where('position', 0)->get();
    }

    /**
     * @param $parent_id
     * @return mixed
     */
    public static function child($parent_id): mixed
    {
        return Category::where(['parent_id' => $parent_id])->get();
    }

    /**
     * @param $category_id
     * @return mixed
     */
    public static function products($category_id): mixed
    {
        $products = Product::active()->get();
        $product_ids = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if ($category['id'] == $category_id) {
                    $product_ids[] = $product['id'];
                }
            }
        }
        return Product::active()->withCount(['wishlist'])->with('rating')->whereIn('id', $product_ids)->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function all_products($id): mixed
    {
        $cate_ids = self::selfAndDescendantIds((int) $id);

        $products = Product::active()->get();
        $product_ids = [];
        foreach ($products as $product) {
            foreach (json_decode($product['category_ids'], true) as $category) {
                if (in_array($category['id'],$cate_ids)) {
                    $product_ids[] = $product['id'];
                }
            }
        }

        return Product::active()->withCount(['wishlist'])->with('rating')->whereIn('id', $product_ids)->get();
    }
}
