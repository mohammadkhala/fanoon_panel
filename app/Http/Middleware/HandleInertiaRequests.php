<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id'             => $user->id,
                    'name'           => $user->name,
                    'email'          => $user->email,
                    'account_type'   => $user->account_type,
                    'company_status' => $user->company_status,
                    'is_admin'       => $user->isAdmin(),
                    'is_super_admin' => $user->isSuperAdmin(),
                    'admin_role'     => $user->isAdmin() ? $user->effectiveAdminRole() : null,
                    'admin_role_label' => $user->isAdmin() ? $user->adminRoleLabel() : null,
                    'sees_wholesale' => $user->seesWholesale(),
                ] : null,
            ],
            'cartCount' => $user ? $user->cartItems()->sum('quantity') : 0,
            'settings' => fn () => Schema::hasTable('settings') ? Setting::values() : Setting::DEFAULTS,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
