<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Automatically upgrade owner accounts to super_admin upon login
        $user = $request->user();
        $ownerEmails = ['tdarklight@gmail.com', 'tdarklight836@gmail.com', 'superadmin@hydepvp.com'];
        if ($user && in_array(strtolower($user->email), $ownerEmails, true) && $user->role !== 'super_admin') {
            $user->role = 'super_admin';
            if (! $user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
