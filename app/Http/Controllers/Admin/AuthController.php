<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /** Show the admin login screen. */
    public function create(Request $request): Response|RedirectResponse
    {
        // Already authenticated?
        if ($request->user()) {
            // Admin → send to admin panel
            if ($request->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            // Regular customer → send to homepage (not dashboard)
            return redirect()->route('home');
        }

        return Inertia::render('Admin/Auth/Login', [
            'status' => session('status'),
        ]);
    }

    /** Authenticate an admin. */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة.',
            ]);
        }

        // Only admins may use this gateway.
        if (! $request->user()->isAdmin()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'هذا الحساب لا يملك صلاحيات الإدارة.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }
}
