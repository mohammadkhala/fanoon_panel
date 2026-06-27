<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        private Order $order,
        private OrderDetail $order_detail,
        private Branch $branch,
        private Product $product,
        private User $user
    ){}

    /**
     * @return Application|Factory|View
     */
    public function orderIndex(): Factory|View|Application
    {
        if (!session()->has('from_date')) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }

        return view('admin-views.report.order-index');
    }

    /**
     * @return Application|Factory|View
     */
    public function earningIndex(): Factory|View|Application
    {
        if (!session()->has('from_date')) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        return view('admin-views.report.earning-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function setDate(Request $request): RedirectResponse
    {
        $fromDate = \Carbon\Carbon::parse($request['from'])->startOfDay();
        $toDate = Carbon::parse($request['to'])->endOfDay();

        session()->put('from_date', $fromDate);
        session()->put('to_date', $toDate);
        return back();
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function productReport(Request $request): Factory|View|Application
    {
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];
        $branchId = config('feature_flags.single_branch_mode', true)
            ? Helpers::getDefaultBranchId()
            : ($this->branch->orderBy('id')->first()?->id);
        $productId = $request['product_id'];

        $products = $this->product->all();

        $orders = $this->order->with(['branch', 'details'])
            ->where('order_status', 'delivered')
            ->when(!is_null($branchId), fn ($query) => $query->where('branch_id', $branchId))
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->get();

        $data = [];
        $totalSold = 0;
        $totalQuantity = 0;

        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                if ($detail['product_id'] == $request['product_id']) {
                    $price = Helpers::variation_price(json_decode($detail->product_details, true), $detail['variations']) - $detail['discount_on_product'];
                    $orderTotal = $price * $detail['quantity'];
                    $data[] = [
                        'order_id' => $order['id'],
                        'date' => $order['created_at'],
                        'customer' => $order->customer,
                        'price' => $orderTotal,
                        'quantity' => $detail['quantity'],
                    ];
                    $totalSold += $orderTotal;
                    $totalQuantity += $detail['quantity'];
                }
            }
        }

        return view('admin-views.report.product-report', compact('data', 'totalSold', 'totalQuantity', 'products', 'startDate', 'endDate', 'branchId', 'productId'));
    }

    /**
     * @param Request $request
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportProductReport(Request $request): StreamedResponse|string
    {
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];
        $branchId = config('feature_flags.single_branch_mode', true)
            ? Helpers::getDefaultBranchId()
            : $this->branch->orderBy('id')->value('id');

        $orders = $this->order->with(['branch', 'details'])
            ->where('order_status', 'delivered')
            ->when(!is_null($branchId), fn ($query) => $query->where('branch_id', $branchId))
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->get();

        $data = [];

        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                if ($detail['product_id'] == $request['product_id']) {
                    $price = Helpers::variation_price(json_decode($detail->product_details, true), $detail['variations']) - $detail['discount_on_product'];
                    $orderTotal = $price * $detail['quantity'];
                    $data[] = [
                        'Order Id' => $order['id'],
                        'Date' => $order->created_at,
                        'Quantity' => $detail['quantity'],
                        'Amount' => $orderTotal,
                    ];
                }
            }
        }
        return (new FastExcel($data))->download('product-report.xlsx');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function saleReport(Request $request): Factory|View|Application
    {
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];
        $branchId = config('feature_flags.single_branch_mode', true)
            ? Helpers::getDefaultBranchId()
            : ($this->branch->orderBy('id')->first()?->id);

        $orders = $this->order->with(['branch', 'details'])
            ->where('order_status', 'delivered')
            ->when(!is_null($branchId), fn ($query) => $query->where('branch_id', $branchId))
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->pluck('id')->toArray();


        $data = [];
        $totalSold = 0;
        $totalQuantity = 0;

        $orderDetails = $this->order_detail->whereIn('order_id', $orders)->latest()->get();

        foreach ($orderDetails as $detail) {
            $price = Helpers::variation_price(json_decode($detail->product_details, true), $detail['variations']) - $detail['discount_on_product'];
            $orderTotal = $price * $detail['quantity'];
            $data[] = [
                'order_id' => $detail['order_id'],
                'date' => $detail['created_at'],
                'price' => $orderTotal,
                'quantity' => $detail['quantity'],
            ];
            $totalSold += $orderTotal;
            $totalQuantity += $detail['quantity'];
        }

        return view('admin-views.report.sale-report', compact('orders', 'data', 'totalSold', 'totalQuantity', 'startDate', 'endDate', 'branchId'));
    }

    /**
     * @param Request $request
     * @return string|StreamedResponse
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function exportSaleReport(Request $request): StreamedResponse|string
    {
        $startDate = $request['start_date'];
        $endDate = $request['end_date'];
        $branchId = config('feature_flags.single_branch_mode', true)
            ? Helpers::getDefaultBranchId()
            : $this->branch->orderBy('id')->value('id');

        $orders = $this->order->with(['branch', 'details'])
            ->where('order_status', 'delivered')
            ->when(!is_null($branchId), fn ($query) => $query->where('branch_id', $branchId))
            ->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
                return $query->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate);
            })
            ->latest()
            ->pluck('id')->toArray();

        $data = [];

        foreach ($this->order_detail->whereIn('order_id', $orders)->get() as $detail) {
            $price = Helpers::variation_price(json_decode($detail->product_details, true), $detail['variations']) - $detail['discount_on_product'];
            $orderTotal = $price * $detail['quantity'];
            $data[] = [
                'Order Id' => $detail['order_id'],
                'Date' => $detail['created_at'],
                'Quantity' => $detail['quantity'],
                'Price' => $orderTotal,
            ];
        }
        return (new FastExcel($data))->download('sale-report.xlsx');
    }

    /**
     * تقرير أفضل المنتجات مبيعاً.
     */
    public function bestSellingProducts(Request $request): Factory|View|Application
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $limit = min(50, max(5, (int) ($request->get('limit', 20))));

        $orderIds = $this->order->where('order_status', 'delivered')
            ->when($startDate && $endDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate))
            ->pluck('id');

        $productStats = $this->order_detail->whereIn('order_id', $orderIds)
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * price - COALESCE(discount_on_product, 0)) as total_amount'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();

        $productIds = $productStats->pluck('product_id')->toArray();
        $products = $this->product->whereIn('id', $productIds)->get()->keyBy('id');

        $data = $productStats->map(fn ($s) => [
            'product' => $products->get($s->product_id),
            'total_qty' => (int) $s->total_qty,
            'total_amount' => (float) $s->total_amount,
        ])->filter(fn ($r) => $r['product'])->values();

        return view('admin-views.report.best-selling-products', compact('data', 'startDate', 'endDate', 'limit'));
    }

    /**
     * تصدير تقرير أفضل المنتجات مبيعاً.
     */
    public function exportBestSellingProducts(Request $request): StreamedResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $limit = min(100, max(5, (int) ($request->get('limit', 50))));

        $orderIds = $this->order->where('order_status', 'delivered')
            ->when($startDate && $endDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate))
            ->pluck('id');

        $productStats = $this->order_detail->whereIn('order_id', $orderIds)
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * price - COALESCE(discount_on_product, 0)) as total_amount'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();

        $productIds = $productStats->pluck('product_id')->toArray();
        $products = $this->product->whereIn('id', $productIds)->get()->keyBy('id');

        $rows = $productStats->map(fn ($s, $i) => [
            '#' => $i + 1,
            'Product' => $products->get($s->product_id)?->name ?? '—',
            'Quantity Sold' => (int) $s->total_qty,
            'Total Amount' => round((float) $s->total_amount, 2),
        ])->filter(fn ($r) => $r['Product'] !== '—')->values()->toArray();

        return (new FastExcel($rows))->download('best-selling-products.xlsx');
    }

    /**
     * تقرير أفضل العملاء (حسب إجمالي المشتريات).
     */
    public function topCustomers(Request $request): Factory|View|Application
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $limit = min(50, max(5, (int) ($request->get('limit', 20))));

        $customerStats = $this->order->where('order_status', 'delivered')
            ->where('is_guest', 0)
            ->whereNotNull('user_id')
            ->when($startDate && $endDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate))
            ->select('user_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(order_amount) as total_spent'))
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();

        $userIds = $customerStats->pluck('user_id')->toArray();
        $users = $this->user->whereIn('id', $userIds)->get()->keyBy('id');

        $data = $customerStats->map(fn ($s) => [
            'customer' => $users->get($s->user_id),
            'order_count' => (int) $s->order_count,
            'total_spent' => (float) $s->total_spent,
        ])->filter(fn ($r) => $r['customer'])->values();

        return view('admin-views.report.top-customers', compact('data', 'startDate', 'endDate', 'limit'));
    }

    /**
     * تصدير تقرير أفضل العملاء.
     */
    public function exportTopCustomers(Request $request): StreamedResponse
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $limit = min(100, max(5, (int) ($request->get('limit', 50))));

        $customerStats = $this->order->where('order_status', 'delivered')
            ->where('is_guest', 0)
            ->whereNotNull('user_id')
            ->when($startDate && $endDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate))
            ->select('user_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(order_amount) as total_spent'))
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();

        $userIds = $customerStats->pluck('user_id')->toArray();
        $users = $this->user->whereIn('id', $userIds)->get()->keyBy('id');

        $rows = $customerStats->map(fn ($s, $i) => [
            '#' => $i + 1,
            'Customer' => $users->get($s->user_id) ? ($users->get($s->user_id)->f_name . ' ' . $users->get($s->user_id)->l_name) : '—',
            'Email' => $users->get($s->user_id)?->email ?? '—',
            'Orders' => (int) $s->order_count,
            'Total Spent' => round((float) $s->total_spent, 2),
        ])->filter(fn ($r) => $r['Customer'] !== '—')->values()->toArray();

        return (new FastExcel($rows))->download('top-customers.xlsx');
    }
}
