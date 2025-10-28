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

        $user = auth()->user();

        // Ha a felhasználó scanner profilhoz tartozik
        if ($user->scanner) {
            return redirect()->route('scanner.dashboard');
        }

        // Ha a felhasználó partner admin és van hozzárendelt gym
        if ($user->is_admin && $user->gyms()->exists()) {
            return redirect()->route('partner.dashboard');
        }

        // Ha admin
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Alapértelmezett
        return redirect()->intended(route('home'));
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
