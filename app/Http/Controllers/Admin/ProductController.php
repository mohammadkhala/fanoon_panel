<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductUserTypeDiscount;
use App\Models\ProductUserTypePrice;
use App\Models\Review;
use App\Models\DesignTemplate;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\UserType;
use App\Services\GoogleTranslateService;
use App\Traits\UploadSizeHelperTrait;
use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Exception\UnsupportedTypeException;
use Box\Spout\Writer\Exception\WriterNotOpenedException;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private Category $category,
        private Product $product,
        private Review $review,
        private Translation $translation
    ){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function variantCombination(Request $request): JsonResponse
    {
        $options = [];
        $price = $request->price;
        $productName = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                if (!$request->has($name)) {
                    continue;
                }
                // يجب أن يطابق منطق store/update وإلا تختلف التركيبات عن حقول stock_* / price_*
                $my_str = implode('|', preg_replace('/\s+/', ' ', $request[$name]));
                $options[] = explode(',', $my_str);
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        $combinations = $result;
        return response()->json([
            'view' => view('admin-views.product.partials._variant-combinations', compact('combinations', 'price', 'productName'))->render(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCategories(Request $request): JsonResponse
    {
        $categories = $this->category->where(['parent_id' => $request->parent_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select subcategory---</option>';
        foreach ($categories as $row) {
            $safeName = e($row->name);
            if ($row->id == $request->sub_category) {
                $res .= '<option value="' . $row->id . '" selected >' . $safeName . '</option>';
            } else {
                $res .= '<option value="' . $row->id . '">' . $safeName . '</option>';
            }
        }
        return response()->json([
            'options' => $res,
        ]);
    }

    /**
     * Translate text using Google Translate API.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function translate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|string|max:50000',
            'source_lang' => 'required|string|size:2',
            'target_lang' => 'required|string|size:2',
            'is_html' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid request'], 422);
        }

        $allowedLangs = [];
        $langSetting = BusinessSetting::where('key', 'language')->first();
        if ($langSetting && $langSetting->value) {
            $allowedLangs = json_decode($langSetting->value, true) ?? [];
        }

        $sourceLang = $request->source_lang;
        $targetLang = $request->target_lang;

        if (!in_array($sourceLang, $allowedLangs) || !in_array($targetLang, $allowedLangs)) {
            return response()->json(['success' => false, 'message' => 'Invalid language'], 422);
        }

        if ($sourceLang === $targetLang) {
            return response()->json([
                'success' => true,
                'translated_text' => $request->text,
            ]);
        }

        $service = app(GoogleTranslateService::class);
        if (!$service->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => translate('Google Translate API is not configured. Please add GOOGLE_TRANSLATE_API_KEY to .env'),
            ], 503);
        }

        $translated = $service->translate(
            $request->text,
            $targetLang,
            $sourceLang,
            (bool) $request->boolean('is_html')
        );

        if ($translated === null) {
            return response()->json([
                'success' => false,
                'message' => translate('Translation failed. Please try again.'),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'translated_text' => $translated,
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $categories = $this->category->where(['parent_id' => 0])->orderBy('position')->get();
        $userTypes = UserType::orderBy('id')->get();
        $productsForRelated = $this->product->withoutGlobalScopes()->orderBy('name')->limit(500)->get(['id', 'name']);
        $designTemplates = DesignTemplate::active()->orderBy('position')->orderBy('id', 'desc')->get(['id', 'name', 'thumbnail', 'category_id', 'product_id']);
        return view('admin-views.product.index', compact('categories', 'userTypes', 'productsForRelated', 'designTemplates'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): View|Factory|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $subCategoryId = $request->query('sub_category_id');
        $status = $request->query('status');
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        $sortBy = $request->query('sort_by', 'latest');
        $stockFilter = $request->query('stock_filter', 'all');
        $tagId = $request->query('tag_id');
        $ratingMin = $request->query('rating_min');
        $attributeId = $request->query('attribute_id');

        if ($search !== null && $search !== '') {
            $queryParams['search'] = $search;
        }
        if ($categoryId !== null && $categoryId !== '') {
            $queryParams['category_id'] = $categoryId;
        }
        if ($subCategoryId !== null && $subCategoryId !== '') {
            $queryParams['sub_category_id'] = $subCategoryId;
        }
        if ($status !== null && $status !== '') {
            $queryParams['status'] = $status;
        }
        if ($priceMin !== null && $priceMin !== '') {
            $queryParams['price_min'] = $priceMin;
        }
        if ($priceMax !== null && $priceMax !== '') {
            $queryParams['price_max'] = $priceMax;
        }
        if ($sortBy !== null && $sortBy !== '') {
            $queryParams['sort_by'] = $sortBy;
        }
        if ($stockFilter !== null && $stockFilter !== '') {
            $queryParams['stock_filter'] = $stockFilter;
        }
        if ($tagId !== null && $tagId !== '') {
            $queryParams['tag_id'] = $tagId;
        }
        if ($ratingMin !== null && $ratingMin !== '' && is_numeric($ratingMin)) {
            $queryParams['rating_min'] = $ratingMin;
        }
        if ($attributeId !== null && $attributeId !== '' && is_numeric($attributeId)) {
            $queryParams['attribute_id'] = $attributeId;
        }

        if (($categoryId === null || $categoryId === '') && $subCategoryId !== null && $subCategoryId !== '') {
            $selectedSubCategory = $this->category->select(['id', 'parent_id'])->find((int) $subCategoryId);
            if ($selectedSubCategory && (int) $selectedSubCategory->parent_id !== 0) {
                $categoryId = (string) $selectedSubCategory->parent_id;
                $queryParams['category_id'] = $categoryId;
            }
        }

        $query = $this->product;

        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($categoryId !== null && $categoryId !== '') {
            $query = $query->whereRaw(
                'JSON_SEARCH(category_ids, "one", ?, NULL, "$[*].id") IS NOT NULL',
                [(string) $categoryId]
            );
        }

        if ($subCategoryId !== null && $subCategoryId !== '') {
            $query = $query->whereRaw(
                'JSON_SEARCH(category_ids, "one", ?, NULL, "$[*].id") IS NOT NULL',
                [(string) $subCategoryId]
            );
        }

        if ($status !== null && $status !== '') {
            $query = $query->where('status', (int) $status);
        }

        if ($priceMin !== null && $priceMin !== '' && is_numeric($priceMin)) {
            $query = $query->where('price', '>=', (float) $priceMin);
        }
        if ($priceMax !== null && $priceMax !== '' && is_numeric($priceMax)) {
            $query = $query->where('price', '<=', (float) $priceMax);
        }

        $defaultStockAlert = (int) (Helpers::get_business_settings('default_minimum_stock_alert') ?? 5);
        if ($stockFilter === 'in_stock') {
            $query = $query->where('total_stock', '>', 0);
        } elseif ($stockFilter === 'low_stock') {
            // نفس منطق scopeLowStock في الموديل ليتوافق عدد الهيدر مع القائمة (يشمل المخزون 0)
            $query = $query->lowStock($defaultStockAlert);
        } elseif ($stockFilter === 'out_of_stock') {
            $query = $query->where('total_stock', '<=', 0);
        }

        if ($tagId !== null && $tagId !== '') {
            $query = $query->whereHas('tags', fn ($q) => $q->where('tags.id', (int) $tagId));
        }

        if ($ratingMin !== null && $ratingMin !== '' && is_numeric($ratingMin)) {
            $ratingVal = (int) min(5, max(1, (int) $ratingMin));
            $query = $query->whereIn('id', function ($sub) use ($ratingVal) {
                $sub->select('product_id')
                    ->from('reviews')
                    ->groupBy('product_id')
                    ->havingRaw('AVG(rating) >= ?', [$ratingVal]);
            });
        }

        if ($attributeId !== null && $attributeId !== '' && is_numeric($attributeId)) {
            $attrId = (int) $attributeId;
            $query = $query->whereNotNull('attributes')
                ->where('attributes', '!=', '')
                ->where('attributes', '!=', '[]');

            if (DB::getDriverName() === 'sqlite') {
                $query = $query->where(function ($q) use ($attrId) {
                    $q->where('attributes', '=', '[' . $attrId . ']')
                        ->orWhere('attributes', 'like', '[' . $attrId . ',%')
                        ->orWhere('attributes', 'like', '%,' . $attrId . ',%')
                        ->orWhere('attributes', 'like', '%,' . $attrId . ']');
                });
            } else {
                $query = $query->whereJsonContains('attributes', $attrId);
            }
        }

        $lowStockOrder = "CASE WHEN total_stock > 0 AND ((minimum_stock_alert IS NOT NULL AND total_stock <= minimum_stock_alert) OR (minimum_stock_alert IS NULL AND total_stock <= ?)) THEN 0 ELSE 1 END ASC";
        $query = $query->orderByRaw($lowStockOrder, [$defaultStockAlert]);

        switch ($sortBy) {
            case 'price_low':
                $query = $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query = $query->orderBy('price', 'desc');
                break;
            case 'name_az':
                $query = $query->orderBy('name', 'asc');
                break;
            case 'name_za':
                $query = $query->orderBy('name', 'desc');
                break;
            case 'stock_low':
                $query = $query->orderBy('total_stock', 'asc');
                break;
            case 'latest':
            default:
                $query = $query->latest();
                break;
        }

        $products = $query->with('tags')->paginate($perPage)->appends($queryParams);
        $categories = $this->category->where(['parent_id' => 0])->orderBy('position')->orderBy('name')->get();
        $subCategories = $categoryId
            ? $this->category->where('parent_id', $categoryId)->orderBy('name')->get()
            : collect();
        $tags = Tag::orderBy('sort_order')->orderBy('name')->get(['id', 'name']);
        $attributes = Attribute::withoutGlobalScopes()->orderBy('name')->get(['id', 'name']);

        return view('admin-views.product.list', compact('products', 'search', 'perPage', 'categories', 'subCategories', 'categoryId', 'subCategoryId', 'status', 'priceMin', 'priceMax', 'sortBy', 'stockFilter', 'tagId', 'ratingMin', 'attributeId', 'tags', 'attributes', 'defaultStockAlert'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $products = $this->product->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.product.partials._table', compact('products'))->render()
        ]);
    }

    /**
     * Autocomplete product names for search suggestions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $q = trim((string) ($request->query('q') ?? ''));
        if (strlen($q) < 2) {
            return response()->json(['suggestions' => []]);
        }
        $products = $this->product
            ->where('name', 'like', '%' . $q . '%')
            ->select('id', 'name')
            ->limit(10)
            ->get()
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name]);
        return response()->json(['suggestions' => $products]);
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function view($id): Factory|View|Application
    {
        $product = $this->product->where(['id' => $id])->first();
        $reviews = $this->review->where(['product_id' => $id])->latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.product.view', compact('product', 'reviews'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->initUploadLimits();
        $check = $this->validateUploadedFile($request, ['images']);
        if ($check !== true) {
            return $check;
        }

        $defaultIdx = $this->resolveDefaultLangIndex($request);
        $primaryLocale = $this->primaryLocaleFromRequest($request, $defaultIdx);
        $nameField = 'name.' . $defaultIdx;
        $descField = 'description.' . $defaultIdx;

        $validator = Validator::make($request->all(), [
            $nameField => 'required|unique:products,name',
            $descField => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|in:kg,gm,ltr,pc',
            'tax' => 'nullable|numeric',
            'tax_type' => 'nullable|string|in:percent,amount',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|string|in:percent,amount',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'total_stock' => 'required|numeric|min:0',
            'images'   => 'required|array|min:1',
            'images.*' => 'image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'choice_no' => 'sometimes|array',
            'choice.*'  => 'sometimes|string',
            'choice_options_*' => 'sometimes|array',
        ], [
            $nameField . '.required' => 'Product name is required!',
            'category_id.required' => 'Category  is required!',
            'images.*.max' => 'Each image may not be greater than ' . $this->maxImageSizeReadable . '.',
            'images.*.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'images.*' => 'image'
        ]);

        if ($request->filled('discount') && $request->filled('discount_type')) {
            if ($request['discount_type'] == 'percent') {
                $discount = ($request['price'] / 100) * $request['discount'];
            } else {
                $discount = $request['discount'];
            }
            if ($request['price'] <= $discount) {
                $validator->getMessageBag()->add('discount', 'Discount can not be more or equal to the price!');
            }
        }

        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'price_')) {
                $validator->sometimes($key, 'required|numeric|min:0', function () use ($value) {
                    return true;
                });
            }
            if (Str::startsWith($key, 'stock_')) {
                $validator->sometimes($key, 'required|integer|min:0', function () use ($value) {
                    return true;
                });
            }
        }

        $imageName = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image_data = Helpers::upload('product/', APPLICATION_IMAGE_FORMAT, $img);
                $imageName[] = $image_data;
            }
            $image_data = json_encode($imageName);
        } else {
            $image_data = json_encode([]);
        }

        $product= new Product;
        $product->name = $request->name[$defaultIdx];

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $product->category_ids = json_encode($category);
        $product->description = $request->description[$defaultIdx] ?? '';

        $choiceOptions = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add($str, 'Attribute choice option values can not be empty!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choiceOptions[] = $item;
            }
        }

        $product->choice_options = json_encode($choiceOptions);
        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }
        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);

        $stockCount = 0;
        $firstCombinationRow = $combinations[0] ?? [];
        if (count($firstCombinationRow) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $stock = $this->variantStockFromRequest($request, $str);
                $item = [];
                $item['type'] = $str;
                $item['price'] = $this->variantPriceFromRequest($request, $str, (float) ($request->price ?? 0));
                $item['stock'] = $stock;
                $variations[] = $item;
                $stockCount += $stock;
            }
        } else {
            $stockCount = (integer) $request->input('total_stock', 0);
        }

        // لا نقارن total_stock المرسل مع مجموع المتغيرات: الحقل قد يكون غير متزامن مع حقول stock_* (مثلاً بعد تغيير التركيبات أو الكاش).
        // القيمة المحفوظة دائماً $stockCount.

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->unit = $request->unit ?: 'pc';
        $product->image = $image_data;

        $product->tax = (float) ($request->tax ?? 0);
        $product->tax_type = $request->tax_type ?? 'percent';

        $firstDiscount = null;
        if ($request->has('user_type_discount') && is_array($request->user_type_discount)) {
            foreach ($request->user_type_discount as $userTypeId => $discount) {
                if ($discount !== '' && $discount !== null && is_numeric($discount) && (float) $discount >= 0) {
                    $firstDiscount = [
                        'discount' => (float) $discount,
                        'discount_type' => in_array($request->user_type_discount_type[$userTypeId] ?? 'percent', ['percent', 'amount']) ? $request->user_type_discount_type[$userTypeId] : 'percent',
                    ];
                    break;
                }
            }
        }
        if ($firstDiscount) {
            $product->discount = $firstDiscount['discount'];
            $product->discount_type = $firstDiscount['discount_type'];
        } else {
            $product->discount = 0;
            $product->discount_type = 'percent';
        }
        $product->total_stock = $stockCount;
        $product->minimum_stock_alert = $request->filled('minimum_stock_alert') ? (int) $request->minimum_stock_alert : null;

        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);

        // ── Product Visibility by User Type ──
        // 'visible_everyone' checkbox is only present in the form data when checked,
        // so a missing key means the admin unchecked it to restrict visibility.
        if ($request->boolean('visible_everyone', false)) {
            $product->visible_to_user_types = null;
        } else {
            $rawIds = $request->input('visible_to_user_types', []);
            $product->visible_to_user_types = array_map('intval', (array) $rawIds);
        }

        $product->save();

        Cache::forget('admin_header_low_stock');

        $product->tags()->sync($request->tag_ids ?? []);
        $relatedIds = array_values(array_filter(array_map('intval', (array) ($request->related_product_ids ?? [])), fn ($id) => $id > 0 && $id != $product->id));
        $product->relatedProducts()->sync(collect($relatedIds)->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => $i]])->toArray());

        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && strtolower((string) $key) !== $primaryLocale) {
                $data[] = array(
                    'translationable_type' => Product::class,
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                );
            }
            if (isset($request->description[$index]) && $request->description[$index] && strtolower((string) $key) !== $primaryLocale) {
                $data[] = array(
                    'translationable_type' => Product::class,
                    'translationable_id' => $product->id,
                    'locale' => $key,
                    'key' => 'description',
                    'value' => $request->description[$index],
                );
            }
        }

        $this->translation->insert($data);

        // Save design template if admin designed one for this product
        $this->saveProductDesignTemplate($request, $product);

        if ($request->has('user_type_price') && is_array($request->user_type_price)) {
            foreach ($request->user_type_price as $userTypeId => $price) {
                $userTypeId = (int) $userTypeId;
                if ($userTypeId <= 0) {
                    continue;
                }
                if ($price !== '' && $price !== null && is_numeric($price) && (float) $price >= 0) {
                    ProductUserTypePrice::create([
                        'product_id' => $product->id,
                        'user_type_id' => $userTypeId,
                        'price' => (float) $price,
                    ]);
                }
            }
        }

        if ($request->has('user_type_discount') && is_array($request->user_type_discount)) {
            foreach ($request->user_type_discount as $userTypeId => $discount) {
                $userTypeId = (int) $userTypeId;
                if ($userTypeId <= 0) {
                    continue;
                }
                $discountType = $request->user_type_discount_type[$userTypeId] ?? 'percent';
                if ($discount !== '' && $discount !== null && is_numeric($discount) && (float) $discount >= 0) {
                    ProductUserTypeDiscount::create([
                        'product_id' => $product->id,
                        'user_type_id' => $userTypeId,
                        'discount' => (float) $discount,
                        'discount_type' => in_array($discountType, ['percent', 'amount']) ? $discountType : 'percent',
                    ]);
                }
            }
        }

        return response()->json([], 200);
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): Factory|View|Application|RedirectResponse
    {
        $product = $this->product->withoutGlobalScopes()->with(['translations', 'userTypePrices', 'userTypeDiscounts', 'tags', 'relatedProducts'])->find($id);
        if (!$product) {
            Toastr::error(translate('Product not found'));
            return redirect()->route('admin.product.list');
        }
        $product_category = json_decode($product->category_ids);
        if (!is_array($product_category)) {
            $product_category = [];
        }
        $categories = $this->category->where(['parent_id' => 0])->get();
        $userTypes = UserType::orderBy('id')->get();
        $productsForRelated = $this->product->withoutGlobalScopes()->where('id', '!=', $id)->orderBy('name')->limit(500)->get(['id', 'name']);
        $selectedTagIds  = $product->tags->pluck('id')->toArray();
        $designTemplates = DesignTemplate::active()->orderBy('position')->orderBy('id', 'desc')->get(['id', 'name', 'thumbnail', 'category_id', 'product_id']);
        return view('admin-views.product.edit', compact('product', 'product_category', 'categories', 'userTypes', 'productsForRelated', 'selectedTagIds', 'designTemplates'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->status = $request->status;
        $product->save();
        Cache::forget('admin_header_low_stock');
        Toastr::success(translate('Product status updated!'));
        return back();
    }

    /**
     * تحديث المخزون من قائمة المنتجات (منتجات بلا متغيرات فقط).
     */
    public function quickStockUpdate(Request $request, int $id): JsonResponse
    {
        $product = $this->product->withoutGlobalScopes()->find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => translate('Product not found')], 404);
        }

        $vars = json_decode($product->variations ?? '[]', true);
        if (!is_array($vars)) {
            $vars = [];
        }
        if (count($vars) > 0) {
            return response()->json([
                'success' => false,
                'message' => translate('quick_stock_variants_hint'),
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'total_stock' => 'required|integer|min:0|max:100000000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('total_stock'),
                'errors' => Helpers::error_processor($validator),
            ], 422);
        }

        $product->total_stock = (int) $request->input('total_stock');
        $product->save();
        Cache::forget('admin_header_low_stock');

        return response()->json([
            'success' => true,
            'total_stock' => $product->total_stock,
            'message' => translate('quick_stock_saved'),
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $this->initUploadLimits();
        $check = $this->validateUploadedFile($request, ['images']);
        if ($check !== true) {
            return $check;
        }

        $defaultIdx = $this->resolveDefaultLangIndex($request);
        $primaryLocale = $this->primaryLocaleFromRequest($request, $defaultIdx);
        $nameField = 'name.' . $defaultIdx;
        $descField = 'description.' . $defaultIdx;

        $validator = Validator::make($request->all(), [
            $nameField => [
                'required',
                Rule::unique('products', 'name')->ignore($id),
            ],
            $descField => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|in:kg,gm,ltr,pc',
            'tax' => 'nullable|numeric',
            'tax_type' => 'nullable|string|in:percent,amount',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable|string|in:percent,amount',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'total_stock' => 'required|numeric|min:0',
            'images'   => 'sometimes|array|min:1',
            'images.*' => 'image|max:' . $this->maxImageSizeKB . '|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'choice_no' => 'sometimes|array',
            'choice.*'  => 'sometimes|string',
            'choice_options_*' => 'sometimes|array',
        ], [
            $nameField . '.required' => 'Product name is required!',
            'category_id.required' => 'Category  is required!',
            'images.*.max' => 'Each image may not be greater than ' . $this->maxImageSizeReadable . '.',
            'images.*.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'images.*' => 'image'
        ]);

        if ($request->filled('discount') && $request->filled('discount_type')) {
            if ($request['discount_type'] == 'percent') {
                $discount = ($request['price'] / 100) * $request['discount'];
            } else {
                $discount = $request['discount'];
            }
            if ($request['price'] <= $discount) {
                $validator->getMessageBag()->add('discount', 'Discount can not be more or equal to the price!');
            }
        }

        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'price_')) {
                $validator->sometimes($key, 'required|numeric|min:0', function () use ($value) {
                    return true;
                });
            }
            if (Str::startsWith($key, 'stock_')) {
                $validator->sometimes($key, 'required|integer|min:0', function () use ($value) {
                    return true;
                });
            }
        }

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $product = $this->product->withoutGlobalScopes()->find($id);
        if (!$product) {
            return response()->json(['errors' => [['code' => 'product', 'message' => translate('Product not found')]]], 404);
        }

        $imagesDecoded = json_decode($product->image ?? '[]', true);
        $images = is_array($imagesDecoded) ? $imagesDecoded : [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $image_data = Helpers::upload('product/', APPLICATION_IMAGE_FORMAT, $img);
                $images[] = $image_data;
            }

        }

        if (!count($images)) {
            $validator->getMessageBag()->add('images', 'Image can not be empty!');
        }

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $product->name = $request->name[$defaultIdx];
        $product->description = $request->description[$defaultIdx] ?? '';

        $category = [];
        if ($request->category_id != null) {
            $category[] = [
                'id' => $request->category_id,
                'position' => 1,
            ];
        }
        if ($request->sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_category_id,
                'position' => 2,
            ];
        }
        if ($request->sub_sub_category_id != null) {
            $category[] = [
                'id' => $request->sub_sub_category_id,
                'position' => 3,
            ];
        }

        $product->category_ids = json_encode($category);

        $choiceOptions = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add($str, 'Attribute choice option values can not be empty!');
                    return response()->json(['errors' => Helpers::error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choiceOptions[] = $item;
            }
        }
        $product->choice_options = json_encode($choiceOptions);
        $variations = [];
        $options = [];

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        //Generates the combinations of customer choice options
        $combinations = Helpers::combinations($options);
        $stockCount = 0;
        $firstCombinationRow = $combinations[0] ?? [];
        if (count($firstCombinationRow) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $stock = $this->variantStockFromRequest($request, $str);
                $item = [];
                $item['type'] = $str;
                $item['price'] = $this->variantPriceFromRequest($request, $str, (float) ($request->price ?? 0));
                $item['stock'] = $stock;
                $variations[] = $item;
                $stockCount += $stock;
            }
        } else {
            $stockCount = (integer) $request->input('total_stock', 0);
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        //combinations end
        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->unit = $request->unit ?: 'pc';
        $product->image = json_encode($images);

        $product->tax = (float) ($request->tax ?? 0);
        $product->tax_type = $request->tax_type ?? 'percent';

        $firstDiscount = null;
        if ($request->has('user_type_discount') && is_array($request->user_type_discount)) {
            foreach ($request->user_type_discount as $userTypeId => $discount) {
                if ($discount !== '' && $discount !== null && is_numeric($discount) && (float) $discount >= 0) {
                    $firstDiscount = [
                        'discount' => (float) $discount,
                        'discount_type' => in_array($request->user_type_discount_type[$userTypeId] ?? 'percent', ['percent', 'amount']) ? $request->user_type_discount_type[$userTypeId] : 'percent',
                    ];
                    break;
                }
            }
        }
        if ($firstDiscount) {
            $product->discount = $firstDiscount['discount'];
            $product->discount_type = $firstDiscount['discount_type'];
        } else {
            $product->discount = $product->discount ?? 0;
            $product->discount_type = $product->discount_type ?? 'percent';
        }
        $product->total_stock = $stockCount;
        $product->minimum_stock_alert = $request->filled('minimum_stock_alert') ? (int) $request->minimum_stock_alert : null;

        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);

        // ── Product Visibility by User Type ──
        // 'visible_everyone' checkbox is only present in the form data when checked,
        // so a missing key means the admin unchecked it to restrict visibility.
        if ($request->boolean('visible_everyone', false)) {
            $product->visible_to_user_types = null;
        } else {
            $rawIds = $request->input('visible_to_user_types', []);
            $product->visible_to_user_types = array_map('intval', (array) $rawIds);
        }

        $product->save();

        Cache::forget('admin_header_low_stock');

        $product->tags()->sync($request->tag_ids ?? []);
        $relatedIds = array_values(array_filter(array_map('intval', (array) ($request->related_product_ids ?? [])), fn ($id) => $id > 0 && $id != $product->id));
        $product->relatedProducts()->sync(collect($relatedIds)->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => $i]])->toArray());

        foreach ($request->lang as $index => $key) {
            if ($request->name[$index] && strtolower((string) $key) !== $primaryLocale) {
                $this->translation->updateOrInsert(
                    ['translationable_type' => Product::class,
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'name'],
                    ['value' => $request->name[$index]]
                );
            }
            if (isset($request->description[$index]) && $request->description[$index] && strtolower((string) $key) !== $primaryLocale) {
                $this->translation->updateOrInsert(
                    ['translationable_type' => Product::class,
                        'translationable_id' => $product->id,
                        'locale' => $key,
                        'key' => 'description'],
                    ['value' => $request->description[$index]]
                );
            }
        }

        if ($request->has('user_type_price') && is_array($request->user_type_price)) {
            foreach ($request->user_type_price as $userTypeId => $price) {
                $userTypeId = (int) $userTypeId;
                if ($userTypeId <= 0) {
                    continue;
                }
                if ($price !== '' && $price !== null && is_numeric($price) && (float) $price >= 0) {
                    ProductUserTypePrice::updateOrCreate(
                        ['product_id' => $product->id, 'user_type_id' => $userTypeId],
                        ['price' => (float) $price]
                    );
                } else {
                    ProductUserTypePrice::where('product_id', $product->id)->where('user_type_id', $userTypeId)->delete();
                }
            }
        }

        if ($request->has('user_type_discount') && is_array($request->user_type_discount)) {
            foreach ($request->user_type_discount as $userTypeId => $discount) {
                $userTypeId = (int) $userTypeId;
                if ($userTypeId <= 0) {
                    continue;
                }
                $discountType = $request->user_type_discount_type[$userTypeId] ?? 'percent';
                if ($discount !== '' && $discount !== null && is_numeric($discount) && (float) $discount >= 0) {
                    ProductUserTypeDiscount::updateOrCreate(
                        ['product_id' => $product->id, 'user_type_id' => $userTypeId],
                        [
                            'discount' => (float) $discount,
                            'discount_type' => in_array($discountType, ['percent', 'amount']) ? $discountType : 'percent',
                        ]
                    );
                } else {
                    ProductUserTypeDiscount::where('product_id', $product->id)->where('user_type_id', $userTypeId)->delete();
                }
            }
        }

        $this->saveProductDesignTemplate($request, $product);

        return response()->json(['success' => true, 'message' => translate('product updated successfully!')], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        foreach (json_decode($product['image'], true) as $img) {
            if (Storage::disk('public')->exists('product/' . $img)) {
                Storage::disk('public')->delete('product/' . $img);
            }
        }
        $product->delete();
        Toastr::success(translate('Product removed!'));
        return back();
    }

    /**
     * @param $id
     * @param $name
     * @return RedirectResponse
     */
    public function removeImage($id, $name): RedirectResponse
    {
        $product = $this->product->find($id);
        $imageArray = [];
        foreach (json_decode($product['image'], true) as $img) {
            if (strcmp($img, $name) != 0) {
                $imageArray[] = $img;
            }
        }

        if (count($imageArray) == 0) {
            Toastr::warning(translate('Product must have at least one image!'));
            return back();
        }

        if (Storage::disk('public')->exists('product/' . $name)) {
            Storage::disk('public')->delete('product/' . $name);
        }

        $this->product->where(['id' => $id])->update([
            'image' => json_encode($imageArray)
        ]);

        Toastr::success(translate('Image removed successfully!'));
        return back();
    }

    /**
     * @return Application|Factory|View
     */
    public function bulkImportIndex(): Factory|View|Application
    {
        return view('admin-views.product.bulk-import');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkImportProduct(Request $request): RedirectResponse
    {
        $this->initUploadLimits('file');

        $check = $this->validateUploadedFile($request, ['products_file']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'products_file' => 'required|mimes:xlsx,csv,txt|max:' . $this->maxImageSizeKB
        ],
            [
                'products_file.required' => 'The Product file field is empty',
                'products_file.mimes' => 'File type must be xlsx or csv',
                'products_file.max' => 'File size must be less than ' . $this->maxImageSizeReadable
            ]
        );

        $file = $request->file('products_file');
        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'xlsx') {
            if (!extension_loaded('zip') || !class_exists('ZipArchive')) {
                Toastr::error(translate('XLSX import requires the PHP Zip extension. Please use CSV format instead, or install php-zip.'));
                return back();
            }
            try {
                $collections = (new FastExcel)->import($file);
            } catch (\Exception $exception) {
                Toastr::error(translate('You have uploaded a wrong format file, please upload the right file.'));
                return back();
            }
        } else {
            try {
                $collections = collect();
                $handle = fopen($file->getRealPath(), 'r');
                $header = fgetcsv($handle);
                if (!$header) {
                    fclose($handle);
                    Toastr::error(translate('CSV file is empty or has no header row.'));
                    return back();
                }
                $header = array_map('trim', $header);
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) !== count($header)) continue;
                    $collections->push(array_combine($header, array_map('trim', $row)));
                }
                fclose($handle);
            } catch (\Exception $exception) {
                Toastr::error(translate('Failed to read the CSV file. Please check the format.'));
                return back();
            }
        }

        if ($collections->isEmpty()) {
            Toastr::error(translate('The file contains no data rows.'));
            return back();
        }

        $col_key = ['name','description','price','tax','category_id','sub_category_id','discount','discount_type','tax_type','unit','total_stock'];
        $optional_keys = ['status'];
        $all_allowed_keys = array_merge($col_key, $optional_keys);
        foreach ($collections as $key => $collection) {
            foreach ($collection as $collectionKey => $value) {
                if ($collectionKey != "" && !in_array($collectionKey, $all_allowed_keys)) {
                    Toastr::error(translate('Please upload the correct format file.'));
                    return back();
                }
            }
        }

        $data = [];

        foreach ($collections as $key => $collection) {
            $rowNum = $key + 2;
            foreach ($col_key as $field) {
                if (!isset($collection[$field]) || $collection[$field] === "" || $collection[$field] === null) {
                    Toastr::error(translate('Please fill required fields in row: ') . $rowNum . ' (' . $field . ')');
                    return back();
                }
            }

            if (!is_numeric($collection['price'])) {
                Toastr::error(translate('Price in row must be a number: ') . ($key + 2));
                return back();
            }

            if (!is_numeric($collection['discount'])) {
                Toastr::error(translate('Discount in row must be a number: ') . ($key + 2));
                return back();
            }

            if (!is_numeric($collection['tax'])) {
                Toastr::error(translate('Tax in row must be a number: ') . ($key + 2));
                return back();
            }

            if (!is_numeric($collection['total_stock'])) {
                Toastr::error(translate('Total stock in row must be a number: ') . ($key + 2));
                return back();
            }

            $product = [
                'discount_type' => $collection['discount_type'],
                'discount' => $collection['discount'],
            ];
            if ($collection['price'] <= Helpers::discount_calculate($product, $collection['price'])) {
                Toastr::error(translate('Discount cannot be greater than or equal to the price!'));
                return back();
            }
        }

        foreach ($collections as $collection) {
            $data[] = [
                'name' => $collection['name'],
                'description' => $collection['description'],
                'image' => json_encode(['def.png']),
                'price' => $collection['price'],
                'variations' => json_encode([]),
                'tax' => $collection['tax'],
                'status' => isset($collection['status']) && $collection['status'] !== '' ? (int)$collection['status'] : 1,
                'attributes' => json_encode([]),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 0], ['id' => (string)$collection['sub_category_id'], 'position' => 1]]),
                'choice_options' => json_encode([]),
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'tax_type' => $collection['tax_type'],
                'unit' => $collection['unit'],
                'total_stock' => $collection['total_stock'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        try {
            DB::beginTransaction();
            foreach (array_chunk($data, 100) as $chunk) {
                DB::table('products')->insert($chunk);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk import failed: ' . $e->getMessage());
            Toastr::error(translate('Failed to import products: ') . $e->getMessage());
            return back();
        }

        Cache::forget('admin_header_low_stock');
        Toastr::success(count($data) . translate('_Products imported successfully'));
        return back();
    }

    /**
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function bulkExportProduct(): StreamedResponse|string
    {
        $storage = [];
        $products = $this->product->all();

        foreach ($products as $item) {
            $categoryId = 0;
            $subCategoryId = 0;
            foreach (json_decode($item->category_ids, true) as $category) {
                if ($category['position'] == 1) {
                    $categoryId = $category['id'];
                } else if ($category['position'] == 2) {
                    $subCategoryId = $category['id'];
                }
            }

            if (!isset($item->name)) {
                $item->name = 'Unnamed Product';
            }

            if (!isset($item->description)) {
                $item->description = 'No description available';
            }

            $storage[] = [
                'name' => $item->name,
                'description' => $item->description,
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'price' => $item->price,
                'tax' => $item->tax,
                'status' => $item->status,
                'discount' => $item->discount,
                'discount_type' => $item->discount_type,
                'tax_type' => $item->tax_type,
                'unit' => $item->unit,
                'total_stock' => $item->total_stock,
            ];
        }

        return (new FastExcel($storage))->download('products.xlsx');
    }

    /**
     * Locale code at the same index as products.name (from submitted lang[]), lowercased.
     */
    private function primaryLocaleFromRequest(Request $request, int $defaultIdx): string
    {
        $langOrder = $request->input('lang', []);
        if (is_array($langOrder) && array_key_exists($defaultIdx, $langOrder)) {
            return strtolower((string) $langOrder[$defaultIdx]);
        }

        return strtolower((string) config('app.locale', 'ar'));
    }

    /**
     * Match submitted lang[] order to config app locale (case-insensitive) for name/description rules and storage index.
     */
    private function resolveDefaultLangIndex(Request $request): int
    {
        $defaultLocale = strtolower((string) config('app.locale', 'ar'));
        $langOrder = $request->input('lang', []);
        if (! is_array($langOrder)) {
            return 0;
        }
        foreach ($langOrder as $idx => $lang) {
            if (strtolower((string) $lang) === $defaultLocale) {
                return (int) $idx;
            }
        }

        return 0;
    }

    /**
     * قراءة مخزون المتغير من الطلب رغم اختلاف تسمية الحقل (نقاط، شرطات، تحويل PHP للمفاتيح).
     */
    private function variantStockFromRequest(Request $request, string $typeStr): int
    {
        $candidates = array_unique(array_filter([
            'stock_' . str_replace('.', '_', $typeStr),
            'stock_' . $typeStr,
        ]));
        foreach ($candidates as $key) {
            if ($request->has($key) && is_numeric($request->input($key))) {
                return (int) max(0, (int) $request->input($key));
            }
        }
        foreach ($request->all() as $key => $value) {
            if (! is_string($key) || ! Str::startsWith($key, 'stock_')) {
                continue;
            }
            if ($value === '' || $value === null || ! is_numeric($value)) {
                continue;
            }
            $suffix = substr($key, strlen('stock_'));
            if ($suffix === str_replace('.', '_', $typeStr) || $suffix === $typeStr) {
                return (int) max(0, (int) $value);
            }
        }

        return 0;
    }

    private function variantPriceFromRequest(Request $request, string $typeStr, float $fallback): float
    {
        $candidates = array_unique(array_filter([
            'price_' . str_replace('.', '_', $typeStr),
            'price_' . $typeStr,
        ]));
        foreach ($candidates as $key) {
            if ($request->has($key) && is_numeric($request->input($key))) {
                return (float) abs((float) $request->input($key));
            }
        }
        foreach ($request->all() as $key => $value) {
            if (! is_string($key) || ! Str::startsWith($key, 'price_')) {
                continue;
            }
            if ($value === '' || $value === null || ! is_numeric($value)) {
                continue;
            }
            $suffix = substr($key, strlen('price_'));
            if ($suffix === str_replace('.', '_', $typeStr) || $suffix === $typeStr) {
                return (float) abs((float) $value);
            }
        }

        return (float) abs($fallback);
    }

    private function saveProductDesignTemplate(Request $request, Product $product): void
    {
        // Case 1: user selected an existing template from the picker
        $selectedId = (int) $request->input('tmpl_selected_id', 0);
        if ($selectedId > 0) {
            $source = DesignTemplate::find($selectedId);
            if ($source) {
                // Remove any existing product-specific template
                DesignTemplate::where('product_id', $product->id)->delete();

                // Duplicate selected template and link to this product
                $copy = $source->replicate();
                $copy->product_id  = $product->id;
                $copy->category_id = $product->category_id ?? $source->category_id;
                $copy->name        = $product->name ?: $source->name;
                $copy->save();

                \Illuminate\Support\Facades\Cache::forget(CACHE_DESIGN_TEMPLATES);
            }
            return;
        }

        // Case 2: user drew a custom template inline (legacy embed path — kept for edit page)
        $canvasJson = $request->input('tmpl_canvas_json', '');
        if (empty($canvasJson) || $canvasJson === 'null') {
            return;
        }
        json_decode($canvasJson);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        $template = DesignTemplate::firstOrNew(['product_id' => $product->id]);
        $template->name          = $product->name ?: ('قالب #' . $product->id);
        $template->product_id    = $product->id;
        $template->category_id   = $product->category_id ?? null;
        $template->canvas_json   = $canvasJson;
        $template->canvas_width  = max(100, (int) $request->input('tmpl_canvas_width', 800));
        $template->canvas_height = max(100, (int) $request->input('tmpl_canvas_height', 800));
        $template->status        = 1;
        $template->position      = 0;

        $thumbnail = $request->input('tmpl_thumbnail_base64', '');
        if ($thumbnail && str_starts_with($thumbnail, 'data:image')) {
            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $thumbnail));
            if ($data && strlen($data) <= 3 * 1024 * 1024) {
                if ($template->thumbnail) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('design-templates/' . $template->thumbnail);
                }
                $filename = \Illuminate\Support\Str::uuid() . '.png';
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('design-templates');
                \Illuminate\Support\Facades\Storage::disk('public')->put('design-templates/' . $filename, $data);
                $template->thumbnail = $filename;
            }
        }

        $template->save();
        \Illuminate\Support\Facades\Cache::forget(CACHE_DESIGN_TEMPLATES);
    }
}
