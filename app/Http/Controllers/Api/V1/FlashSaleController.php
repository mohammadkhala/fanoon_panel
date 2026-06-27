<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use Illuminate\Http\Request;

class FlashSaleController extends Controller
{
    public function __construct(
        private FlashSale        $flashSale,
        private FlashSaleProduct $flashSaleProduct,
    )
    {
    }

    public function getFlashSale(Request $request): \Illuminate\Http\JsonResponse
    {
        $limit = Helpers::capApiLimit($request['limit'] ?? 10);
        $offset = Helpers::capApiOffset($request['offset'] ?? 1);

        $flashSale = $this->flashSale->active()->first();

        if (!isset($flashSale)) {
            $products = [
                'total_size' => null,
                'limit' => $limit,
                'offset' => $offset,
                'flash_sale' => $flashSale,
                'products' => []
            ];
            return response()->json($products, 200);

        }

        $productIds = $this->flashSaleProduct->with(['product'])
            ->whereHas('product', function ($q) {
                $q->active();
            })
            ->where(['flash_sale_id' => $flashSale->id])
            ->pluck('product_id')
            ->toArray();

        $products = ProductLogic::filterFlashSale(
            flashSaleProductIds: $productIds,
            price_low: $request['price_low'],
            price_high: $request['price_high'],
            rating: $request['rating'],
            category_ids: $request['category_ids'],
            sort_by: $request['sort_by'],
            limit: $limit,
            offset: $offset,
            tag_ids: $request['tag_ids'],
            in_stock_only: (bool) $request->boolean('in_stock_only'),
            attribute_ids: $request['attribute_ids']
        );
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        $products['products'] = Helpers::apply_user_type_prices_to_products($products['products'], true);
        return response()->json($products, 200);

    }
}
