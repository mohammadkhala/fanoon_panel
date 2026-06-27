<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Branch;
use App\Models\Category;
use App\Models\ContactUs;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Traits\UploadSizeHelperTrait;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use function App\Library\businessSettingInsertOrUpdate;

class SystemController extends Controller
{
    use UploadSizeHelperTrait;
    public function __construct(
        private Admin $admin,
        private Branch $branch,
        private Category $category,
        private Order $order,
        private OrderDetail $order_detail,
        private Product $product,
        private User $user
    ){}

    /**
     * @param $id
     * @return string
     */
    public function fcm($id): string
    {
        $fcmToken =  $this->admin->find(auth('admin')->id())->fcm_token;
        $data = [
            'title' => 'New auto generate message arrived from admin dashboard',
            'description' => $id,
            'order_id' => '',
            'image' => '',
            'type' => 'general',
        ];
        Helpers::send_push_notif_to_device($fcmToken, $data);

        return "Notification sent to admin";
    }

    /**
     * @return Application|Factory|View
     */
    public function dashboard(): Factory|View|Application
    {
        $cacheMinutes = 5;
        $topSell = Cache::remember('admin_dashboard_top_sell', $cacheMinutes * 60, function () {
            return $this->order_detail->with(['product'])
                ->whereHas('order', fn ($q) => $q->where('order_status', 'delivered'))
                ->select('product_id', DB::raw('SUM(quantity) as count'))
                ->groupBy('product_id')
                ->orderBy('count', 'desc')
                ->take(6)
                ->get();
        });

        $mostRatedProducts = Cache::remember('admin_dashboard_most_rated', $cacheMinutes * 60, function () {
            return $this->product->rightJoin('reviews', 'reviews.product_id', '=', 'products.id')
                ->groupBy('product_id')
                ->select(['product_id',
                    DB::raw('AVG(reviews.rating) as ratings_average'),
                    DB::raw('count(*) as total')
                ])
                ->orderBy('total', 'desc')
                ->take(6)
                ->get();
        });

        $topCustomer = Cache::remember('admin_dashboard_top_customer', $cacheMinutes * 60, function () {
            return $this->order->with(['customer'])
                ->select('user_id', DB::raw('COUNT(user_id) as count'))
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->take(6)
                ->get();
        });

        $data = self::order_stats_data();

        $data['customer'] = $this->user->count();
        $data['product'] =  $this->product->count();
        $data['order'] = $this->order->count();
        $data['category'] = $this->category->where('parent_id', 0)->count();
        $data['branch'] = $this->branch->count();

        $data['top_sell'] = $topSell;
        $data['most_rated_products'] = $mostRatedProducts;
        $data['top_customer'] = $topCustomer;
        $defaultStockAlert = (int) (Helpers::get_business_settings('default_minimum_stock_alert') ?? 5);
        $data['low_stock_products'] = $this->product->lowStock($defaultStockAlert)->take(10)->get();

        $from = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
        $to = \Illuminate\Support\Carbon::now()->endOfYear()->format('Y-m-d');

        /*earning statistics chart*/

        $earning = [];
        $earningData = $this->order->where([
            'order_status' => 'delivered'
        ])->select(
            DB::raw('IFNULL(sum(order_amount),0) as sums'),
            DB::raw('YEAR(created_at) year, MONTH(created_at) month')
        )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();
        for ($inc = 1; $inc <= 12; $inc++) {
            $earning[$inc] = 0;
            foreach ($earningData as $match) {
                if ($match['month'] == $inc) {
                    $earning[$inc] = $match['sums'];
                }
            }
        }
        return view('admin-views.dashboard', compact('data', 'earning'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orderStats(Request $request): JsonResponse
    {
        session()->put('statistics_type', $request['statistics_type']);
        $data = self::order_stats_data();

        return response()->json([
            'view' => view('admin-views.partials._dashboard-order-stats', compact('data'))->render()
        ], 200);
    }

    /**
     * بحث موحّد في الطلبات والمنتجات والعملاء.
     */
    public function unifiedSearch(Request $request): JsonResponse
    {
        $q = mb_substr(trim((string) ($request->query('q') ?? '')), 0, 100);
        if (mb_strlen($q) < 2) {
            return response()->json(['orders' => [], 'products' => [], 'customers' => []]);
        }

        $orders = $this->order->notPos()
            ->where(function ($qry) use ($q) {
                $qry->where('id', 'like', "%{$q}%")
                    ->orWhere('order_status', 'like', "%{$q}%")
                    ->orWhere('payment_status', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get(['id', 'order_status', 'order_amount', 'created_at']);

        $products = $this->product->withoutGlobalScopes()
            ->where(function ($qry) use ($q) {
                $qry->where('id', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'price', 'status']);

        $customers = $this->user
            ->where(function ($qry) use ($q) {
                $qry->where('id', 'like', "%{$q}%")
                    ->orWhere('f_name', 'like', "%{$q}%")
                    ->orWhere('l_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ["%{$q}%"]);
            })
            ->limit(5)
            ->get(['id', 'f_name', 'l_name', 'phone', 'email']);

        return response()->json([
            'orders' => $orders->map(fn ($o) => [
                'id' => $o->id,
                'url' => route('admin.orders.details', $o->id),
                'label' => '#' . $o->id . ' — ' . ($o->order_amount ?? 0),
            ]),
            'products' => $products->map(fn ($p) => [
                'id' => $p->id,
                'url' => route('admin.product.edit', $p->id),
                'label' => $p->name . ' — ' . ($p->price ?? 0),
            ]),
            'customers' => $customers->map(fn ($c) => [
                'id' => $c->id,
                'url' => route('admin.customer.view', $c->id),
                'label' => trim($c->f_name . ' ' . $c->l_name) . ' — ' . ($c->phone ?? $c->email ?? ''),
            ]),
        ]);
    }

    /**
     * @return array
     */
    public function order_stats_data(): array
    {
        $today = session()->has('statistics_type') && session('statistics_type') == 'today' ? 1 : 0;
        $this_month = session()->has('statistics_type') && session('statistics_type') == 'this_month' ? 1 : 0;

        $query = $this->order->newQuery();
        $query->when($today, fn ($q) => $q->whereDate('created_at', Carbon::today()));
        $query->when($this_month, fn ($q) => $q->whereMonth('created_at', Carbon::now()));

        $counts = (clone $query)->selectRaw("
            SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN order_status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN order_status = 'processing' THEN 1 ELSE 0 END) as processing,
            SUM(CASE WHEN order_status = 'out_for_delivery' THEN 1 ELSE 0 END) as out_for_delivery,
            SUM(CASE WHEN order_status = 'delivered' THEN 1 ELSE 0 END) as delivered,
            SUM(CASE WHEN order_status = 'returned' THEN 1 ELSE 0 END) as returned,
            SUM(CASE WHEN order_status = 'failed' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN order_status = 'canceled' THEN 1 ELSE 0 END) as canceled,
            COUNT(*) as all_count
        ")->first();

        return [
            'pending' => (int) ($counts->pending ?? 0),
            'confirmed' => (int) ($counts->confirmed ?? 0),
            'processing' => (int) ($counts->processing ?? 0),
            'out_for_delivery' => (int) ($counts->out_for_delivery ?? 0),
            'delivered' => (int) ($counts->delivered ?? 0),
            'all' => (int) ($counts->all_count ?? 0),
            'returned' => (int) ($counts->returned ?? 0),
            'failed' => (int) ($counts->failed ?? 0),
            'canceled' => (int) ($counts->canceled ?? 0),
        ];
    }

    /**
     * @return JsonResponse
     */
    public function storeData(): JsonResponse
    {
        $cacheKey = 'admin_store_data';
        $data = Cache::remember($cacheKey, 10, function () {
            return [
                'new_order' => Order::notPos()->where(['checked' => 0])->count(),
                'pending_type_approval' => User::whereNotNull('requested_user_type_id')->count(),
                'new_contact_us' => ContactUs::unread()->count(),
            ];
        });
        return response()
            ->json([
                'success' => 1,
                'data' => $data,
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    /**
     * @return Application|Factory|View
     */
    public function settings(): Factory|View|Application
    {
        return view('admin-views.settings');
    }

    /**
     * Switch admin panel language (e.g. en, ar).
     * @param string $locale
     * @return RedirectResponse
     */
    public function switchLang(string $locale): RedirectResponse
    {
        $allowed = ['en', 'ar'];
        if (!in_array($locale, $allowed, true)) {
            $locale = 'ar';
        }
        session(['local' => $locale]);
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settingsUpdate(Request $request): RedirectResponse
    {
        $this->initUploadLimits();
        $check = $this->validateUploadedFile($request, ['image']);
        if ($check !== true) {
            return $check;
        }

        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'image' => 'sometimes|image|max:'. $this->maxImageSizeKB .'|mimes:' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
        ], [
            'f_name.required' => 'First name is required!',
            'l_name.required' => 'Last name is required!',
            'image.mimes' => 'Image must be a file of type: ' . implode(',', array_column(IMAGE_EXTENSIONS, 'key')),
            'image.max' => translate('Image size must be below ' . $this->maxImageSizeReadable),
        ]);

        $admin =  $this->admin->find(auth('admin')->id());
        $admin->f_name = $request->f_name;
        $admin->l_name = $request->l_name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $admin->image = $request->has('image') ? Helpers::update('admin/', $admin->image, APPLICATION_IMAGE_FORMAT, $request->file('image')) : $admin->image;
        $admin->save();
        Toastr::success(translate('Admin updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settingsPasswordUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required',
        ]);

        $admin =  $this->admin->find(auth('admin')->id());
        $admin->password = bcrypt($request['password']);
        $admin->save();
        Toastr::success(translate('Admin password updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getEarningStatistics(Request $request): JsonResponse
    {
        $dateType = $request->type ?? 'yearEarn';
        $cacheKey = 'admin_earning_statistics_' . $dateType;
        $cacheSeconds = 600; // 10 دقائق

        $data = Cache::remember($cacheKey, $cacheSeconds, function () use ($dateType) {
            return $this->computeEarningStatistics($dateType);
        });

        return response()->json($data);
    }

    /**
     * حساب إحصائيات الأرباح (تُستدعى من الكاش).
     */
    private function computeEarningStatistics(string $dateType): array
    {
        $earningData = array();
        if ($dateType == 'yearEarn') {
            $number = 12;
            $from = \Illuminate\Support\Carbon::now()->startOfYear()->format('Y-m-d');
            $to = Carbon::now()->endOfYear()->format('Y-m-d');

            $earning = $this->order->where([
                'order_status' => 'delivered'
            ])->select(
                DB::raw('IFNULL(sum(order_amount),0) as sums'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month')
            )->whereBetween('created_at', [$from, $to])->groupby('year', 'month')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $earningData[$inc] = 0;
                foreach ($earning as $match) {
                    if ($match['month'] == $inc) {
                        $earningData[$inc] = $match['sums'];
                    }
                }
            }
            $keyRange = array("Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

        }elseif($dateType == 'MonthEarn') {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
            $number = date('d',strtotime($to));
            $keyRange = range(1, $number);

            $earning = $this->order->where([
                'order_status' => 'delivered'
            ])->select(
                DB::raw('IFNULL(sum(order_amount),0) as sums'),
                DB::raw('YEAR(created_at) year, MONTH(created_at) month, DAY(created_at) day')
            )->whereBetween('created_at', [$from, $to])->groupby('year', 'month', 'day')->get()->toArray();

            for ($inc = 1; $inc <= $number; $inc++) {
                $earningData[$inc] = 0;
                foreach ($earning as $match) {
                    if ($match['day'] == $inc) {
                        $earningData[$inc] = $match['sums'];
                    }
                }
            }

        }elseif($dateType == 'WeekEarn') {
            $from = Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d 00:00:00');
            $to = Carbon::now()->endOfWeek(Carbon::SATURDAY)->format('Y-m-d 23:59:59');

            $dateRange = CarbonPeriod::create($from, $to)->toArray();
            $day_range = [];

            foreach($dateRange as $date) {
                $day_range[] = $date->format('d');
            }

            $day_range = array_flip($day_range);
            $day_range_keys = array_map('intval', array_keys($day_range));

            $earningData = array_fill_keys($day_range_keys, 0);

            $earnings = $this->order->where('order_status', 'delivered')
                ->whereBetween('created_at', [$from, $to])
                ->select(
                    DB::raw('IFNULL(sum(order_amount), 0) as sums'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('DAY(created_at) as day')
                )
                ->groupBy('year', 'month', 'day')
                ->get();

            foreach($earnings as $earning) {
                $day = (int)$earning->day;
                if (isset($earningData[$day])) {
                    $earningData[$day] = $earning->sums;
                }
            }
            $keyRange = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        } else {
            $keyRange = array("Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
            $earningData = array_fill(1, 12, 0);
        }

        $label = $keyRange;
        $earningDataFinal = $earningData;

        return [
            'earning_label' => $label,
            'earning' => array_values($earningDataFinal),
        ];
    }

    public function ignoreCheckOrder()
    {
        $this->order->where(['checked' => 0])->update(['checked' => 1]);
        Cache::forget('admin_store_data');
        foreach (Branch::pluck('id') as $branchId) {
            Cache::forget("branch_store_data_{$branchId}");
        }
        return redirect()->back();
    }

    /**
     * Mark all contact us messages as read (dismiss contact us notification).
     */
    public function ignoreCheckContact(): RedirectResponse
    {
        ContactUs::unread()->update(['read_at' => now()]);
        Cache::forget('admin_store_data');
        return redirect()->back();
    }
}
