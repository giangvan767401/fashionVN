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
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:75'],
            'last_name' => ['required', 'string', 'max:75'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:191', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'full_name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'role_id' => 6, // default: customer
        ]);

        event(new Registered($user));

        // Let Laravel handle login immediately if verification is active, 
        // Auth flow will handle verification notice appropriately.
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
