<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, new \App\Rules\LegitEmail],
            'minecraft_gamertag' => ['nullable', 'string', 'max:32', 'regex:/^[a-zA-Z0-9_ ]+$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $ownerEmails = ['tdarklight@gmail.com', 'superadmin@hydepvp.com'];
        $isOwner = in_array(strtolower($request->email), $ownerEmails, true);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $isOwner ? 'super_admin' : 'member',
            'minecraft_gamertag' => $request->minecraft_gamertag ? trim($request->minecraft_gamertag) : null,
            'password' => Hash::make($request->password),
            'email_verified_at' => $isOwner ? now() : null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
