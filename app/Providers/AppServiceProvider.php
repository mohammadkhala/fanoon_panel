<?php

namespace App\Providers;

use App\CentralLogics\Helpers;
use App\Models\Banner;
use App\Models\Category;
use App\Models\LoginSetup;
use App\Observers\BannerObserver;
use App\Observers\BusinessSettingObserver;
use App\Observers\CategoryObserver;
use App\Observers\LoginSetupObserver;
use App\Observers\OrderObserver;
use App\Observers\ReviewObserver;
use App\Traits\SystemAddonTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use App\Models\BusinessSetting;
use App\Models\ContactUs;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Observers\ContactUsObserver;
use App\Observers\UserAdminNotificationObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    use SystemAddonTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Custom class aliases (facades) used in your app
        $aliases = [
            'Helpers'  => Helpers::class,
            'PDF' => Pdf::class,
        ];

        foreach ($aliases as $alias => $class) {
            if (! class_exists($alias) && class_exists($class)) {
                class_alias($class, $alias);
            }
        }
    }

    /**
     * Bootstrap any application services.
*
     * @return void
     */
    public function boot()
    {
        if(config('app.force_https')) {
            \URL::forceScheme('https');
        }

        BusinessSetting::observe(BusinessSettingObserver::class);
        LoginSetup::observe(LoginSetupObserver::class);
        Banner::observe(BannerObserver::class);
        Category::observe(CategoryObserver::class);
        Order::observe(OrderObserver::class);
        ContactUs::observe(ContactUsObserver::class);
        User::observe(UserAdminNotificationObserver::class);
        Review::observe(ReviewObserver::class);

        //for system addon (POS routes excluded - module disabled)
        $addonRoutes = collect($this->get_addon_admin_routes())->map(function ($routes) {
            return collect($routes)->filter(function ($route) {
                $path = strtolower($route['path'] ?? '');
                $name = strtolower($route['name'] ?? '');
                return !str_contains($path, 'pos') && $name !== 'pos';
            })->values()->all();
        })->filter(fn ($r) => count($r) > 0)->values()->all();
        Config::set('addon_admin_routes', $addonRoutes);
        Config::set('get_payment_publish_status',$this->get_payment_publish_status());

        try {
            $timezone = BusinessSetting::where(['key' => 'time_zone'])->first();
            if (isset($timezone)) {
                config(['app.timezone' => $timezone->value]);
                date_default_timezone_set($timezone->value);
            }
        }catch(\Exception $exception){}

        Paginator::useBootstrap();

        // تنبيه المخزون في هيدر لوحة التحكم (كاش 2 دقيقة)
        View::composer('layouts.admin.partials._header', function ($view) {
            $data = Cache::remember('admin_header_low_stock', 120, function () {
                $products = Product::lowStock()->take(15)->get(['id', 'name', 'total_stock']);
                return [
                    'lowStockCount' => Product::lowStock()->count(),
                    'lowStockProducts' => $products,
                ];
            });
            $view->with('lowStockCount', $data['lowStockCount'])
                 ->with('lowStockProducts', $data['lowStockProducts']);
        });

        // كاش عدد الطلبات في الشريط الجانبي (دقيقة واحدة)
        View::composer('layouts.admin.partials._sidebar', function ($view) {
            $orderCounts = Cache::remember('admin_sidebar_order_counts', 60, function () {
                $base = Order::notPos();
                $all = (clone $base)->count();
                $counts = (clone $base)->selectRaw('order_status, count(*) as c')->groupBy('order_status')->pluck('c', 'order_status')->toArray();
                return [
                    'all' => $all,
                    'pending' => $counts['pending'] ?? 0,
                    'confirmed' => $counts['confirmed'] ?? 0,
                    'processing' => $counts['processing'] ?? 0,
                    'out_for_delivery' => $counts['out_for_delivery'] ?? 0,
                    'delivered' => $counts['delivered'] ?? 0,
                    'returned' => $counts['returned'] ?? 0,
                    'failed' => $counts['failed'] ?? 0,
                    'canceled' => $counts['canceled'] ?? 0,
                ];
            });
            $view->with('sidebarOrderCounts', $orderCounts);
        });
    }
}
