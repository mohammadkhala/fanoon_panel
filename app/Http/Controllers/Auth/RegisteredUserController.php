<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'phone' => 'nullable|string|max:30',
            'account_type' => 'required|in:individual,company',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Company-only fields
            'company_name' => 'required_if:account_type,company|nullable|string|max:255',
            'trade_license_no' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:120',
            'trade_license' => 'required_if:account_type,company|nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $isCompany = $request->account_type === 'company';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'account_type' => $request->account_type,
            // Companies start pending until an admin approves wholesale access
            'company_status' => $isCompany ? 'pending' : null,
        ]);

        if ($isCompany) {
            $path = $request->file('trade_license')?->store('licenses', 'public');

            CompanyProfile::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'trade_license_no' => $request->trade_license_no,
                'trade_license_path' => $path,
                'city' => $request->city,
                'status' => 'pending',
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
