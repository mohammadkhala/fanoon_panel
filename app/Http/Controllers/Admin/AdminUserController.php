<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Users/Index', [
            'admins' => User::where('account_type', 'admin')
                ->orderBy('name')
                ->get()
                ->map(fn (User $u) => [
                    'id'         => $u->id,
                    'name'       => $u->name,
                    'email'      => $u->email,
                    'phone'      => $u->phone,
                    'admin_role' => $u->effectiveAdminRole(),
                    'role_label' => $u->adminRoleLabel(),
                    'created_at' => $u->created_at->format('Y-m-d'),
                ]),
            'roles' => [
                ['value' => User::ROLE_SUPER_ADMIN,        'label' => 'سوبر أدمن'],
                ['value' => User::ROLE_ORDERS_MANAGER,     'label' => 'مدير طلبات'],
                ['value' => User::ROLE_PRODUCTS_MANAGER,   'label' => 'مدير منتجات'],
                ['value' => User::ROLE_GENERAL_SUPERVISOR, 'label' => 'مشرف عام'],
            ],
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|lowercase|email|max:255|unique:users,email',
            'phone'      => 'nullable|string|max:30',
            'admin_role' => 'required|in:super_admin,orders_manager,products_manager,general_supervisor',
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'] ?? null,
            'password'     => Hash::make($data['password']),
            'account_type' => 'admin',
            'admin_role'   => $data['admin_role'],
        ]);

        return back()->with('success', 'تم إنشاء حساب المدير.');
    }

    public function updateRole(Request $request, User $user)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'فقط السوبر أدمن يمكنه تعديل الصلاحيات.');
        abort_unless($user->isAdmin(), 404);
        abort_if($user->id === $request->user()->id, 403, 'لا يمكنك تعديل صلاحياتك الخاصة.');

        $data = $request->validate([
            'admin_role' => 'required|in:super_admin,orders_manager,products_manager,general_supervisor',
        ]);

        $user->update(['admin_role' => $data['admin_role']]);

        return back()->with('success', 'تم تحديث الصلاحية.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()->isSuperAdmin(), 403);
        abort_unless($user->isAdmin(), 404);
        abort_if($user->id === $request->user()->id, 403, 'لا يمكنك حذف حسابك الحالي.');

        $user->delete();

        return back()->with('success', 'تم حذف المدير.');
    }
}
