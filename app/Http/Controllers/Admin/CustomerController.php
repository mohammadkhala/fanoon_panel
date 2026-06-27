<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\UserType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function __construct(
        private Order $order,
        private User $user
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function customerList(Request $request): View|Factory|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];

        $search = $request->query('search');
        $userTypeId = $request->query('user_type_id');
        $hasOrders = $request->query('has_orders');
        $segment = $request->query('segment');

        if ($search !== null && $search !== '') {
            $queryParams['search'] = $search;
        }
        if ($userTypeId !== null && $userTypeId !== '') {
            $queryParams['user_type_id'] = $userTypeId;
        }
        if ($hasOrders !== null && $hasOrders !== '') {
            $queryParams['has_orders'] = $hasOrders;
        }
        if ($segment !== null && $segment !== '') {
            $queryParams['segment'] = $segment;
        }

        $query = $this->user;

        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('f_name', 'like', "%{$search}%")
                    ->orWhere('l_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

if ($userTypeId !== null && $userTypeId !== '') {
            $defaultType = UserType::getDefault();
            if ($defaultType && (int)$userTypeId === (int)$defaultType->id) {
                $query = $query->where(function ($q) use ($userTypeId) {
                    $q->where('user_type_id', $userTypeId)
                        ->orWhereNull('user_type_id');
                });
            } else {
                $query = $query->where('user_type_id', $userTypeId);
            }
        }

        if ($hasOrders === '1') {
            $query = $query->whereHas('orders');
        } elseif ($hasOrders === '0') {
            $query = $query->whereDoesntHave('orders');
        }

        // تقسيم العملاء: vip, frequent, new, inactive
        if ($segment === 'vip') {
            $query = $query->whereHas('orders')->whereIn('id', function ($sub) {
                $sub->select('user_id')->from('orders')->where('is_guest', 0)
                    ->groupBy('user_id')->havingRaw('SUM(order_amount) >= ?', [500]);
            });
        } elseif ($segment === 'frequent') {
            $query = $query->whereHas('orders')->whereIn('id', function ($sub) {
                $sub->select('user_id')->from('orders')->where('is_guest', 0)
                    ->groupBy('user_id')->havingRaw('COUNT(*) >= 5');
            });
        } elseif ($segment === 'new') {
            $cutoff = now()->subDays(30);
            $query = $query->whereHas('orders', fn ($q) => $q->where('created_at', '>=', $cutoff));
        } elseif ($segment === 'inactive') {
            $cutoff = now()->subDays(90);
            $query = $query->whereIn('id', function ($sub) use ($cutoff) {
                $sub->select('user_id')->from('orders')->where('is_guest', 0)
                    ->groupBy('user_id')->havingRaw('MAX(created_at) < ?', [$cutoff]);
            });
        }

        $customers = $query->with(['orders', 'userType', 'requestedUserType', 'loyaltyPoint'])->latest()->paginate($perPage)->appends($queryParams);
        $userTypes = UserType::orderBy('id')->get();
        $loyaltyEnabled = (int) (Helpers::get_business_settings('loyalty_points_enabled') ?? 0);

        return view('admin-views.customer.list', compact('customers', 'search', 'perPage', 'userTypes', 'userTypeId', 'hasOrders', 'segment', 'loyaltyEnabled'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function view($id, Request $request): View|Factory|RedirectResponse|Application
    {
        $perPage = (int)$request->query('per_page', Helpers::getPagination());
        $queryParams = ['per_page' => $perPage];
        $search = $request->query('search');
        $customer = $this->user->find($id);
        if ($search) {
            $queryParams['search'] = $search;
        }
        if (isset($customer)) {
            $orders = $this->order->latest()->where(['user_id' => $id])
                ->when($search, function ($query) use ($search) {
                    $key = explode(' ', $search);
                    foreach ($key as $value) {
                        $query->where('id', 'like', "%$value%");
                    }
                })
                ->paginate($perPage)->appends($queryParams);
            $customer->load(['userType', 'requestedUserType']);
            $userTypes = UserType::orderBy('id')->get();
            return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search', 'perPage', 'userTypes'));
        }
        Toastr::error(translate('Customer not found!'));
        return back();
    }

    public function updateType(Request $request, $id): RedirectResponse
    {
        $request->validate(['user_type_id' => 'required|exists:user_types,id']);
        $customer = $this->user->findOrFail($id);
        $customer->user_type_id = $request->user_type_id;
        $customer->requested_user_type_id = null;
        $customer->save();
        Toastr::success(translate('Customer type updated!'));
        return back();
    }

    public function approveType($id): RedirectResponse
    {
        $customer = $this->user->findOrFail($id);
        if (!$customer->requested_user_type_id) {
            Toastr::info(translate('No pending type to approve.'));
            return back();
        }
        $customer->user_type_id = $customer->requested_user_type_id;
        $customer->requested_user_type_id = null;
        $customer->save();
        Toastr::success(translate('Customer type approved!'));
        return back();
    }

    /**
     * تصدير قائمة العملاء إلى Excel — يطبق نفس الفلاتر (بحث، نوع عميل، لديه طلبات).
     */
    public function exportCustomers(Request $request): StreamedResponse
    {
        $search = $request->query('search');
        $userTypeId = $request->query('user_type_id');
        $hasOrders = $request->query('has_orders');
        $segment = $request->query('segment');

        $query = $this->user;

        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('f_name', 'like', "%{$search}%")
                    ->orWhere('l_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ["%{$search}%"])
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($userTypeId !== null && $userTypeId !== '') {
            $defaultType = UserType::getDefault();
            if ($defaultType && (int)$userTypeId === (int)$defaultType->id) {
                $query = $query->where(function ($q) use ($userTypeId) {
                    $q->where('user_type_id', $userTypeId)
                        ->orWhereNull('user_type_id');
                });
            } else {
                $query = $query->where('user_type_id', $userTypeId);
            }
        }

        if ($hasOrders === '1') {
            $query = $query->whereHas('orders');
        } elseif ($hasOrders === '0') {
            $query = $query->whereDoesntHave('orders');
        }

        if ($segment === 'vip') {
            $query = $query->whereHas('orders')->whereIn('id', function ($sub) {
                $sub->select('user_id')->from('orders')->where('is_guest', 0)
                    ->groupBy('user_id')->havingRaw('SUM(order_amount) >= ?', [500]);
            });
        } elseif ($segment === 'frequent') {
            $query = $query->whereHas('orders')->whereIn('id', function ($sub) {
                $sub->select('user_id')->from('orders')->where('is_guest', 0)
                    ->groupBy('user_id')->havingRaw('COUNT(*) >= 5');
            });
        } elseif ($segment === 'new') {
            $cutoff = now()->subDays(30);
            $query = $query->whereHas('orders', fn ($q) => $q->where('created_at', '>=', $cutoff));
        } elseif ($segment === 'inactive') {
            $cutoff = now()->subDays(90);
            $query = $query->whereIn('id', function ($sub) use ($cutoff) {
                $sub->select('user_id')->from('orders')->where('is_guest', 0)
                    ->groupBy('user_id')->havingRaw('MAX(created_at) < ?', [$cutoff]);
            });
        }

        $customers = $query->withCount('orders')->with(['userType', 'loyaltyPoint'])->latest()->get();

        $storage = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => trim($customer->f_name . ' ' . $customer->l_name),
                'email' => $customer->email ?? '',
                'phone' => $customer->phone ?? '',
                'user_type' => $customer->userType?->name ?? '—',
                'orders_count' => $customer->orders_count ?? 0,
                'loyalty_points' => $customer->loyaltyPoint?->points ?? 0,
                'created_at' => $customer->created_at?->format('Y-m-d H:i'),
            ];
        });

        return (new FastExcel($storage))->download('customers.xlsx');
    }
}
