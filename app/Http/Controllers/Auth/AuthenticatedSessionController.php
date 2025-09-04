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
    public function create()
    {
        $check_login = Auth::check();
        if($check_login){
            return redirect()->intended(route('dashboard', absolute: false));
        }else{
            return view('auth.boxed-signin');
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            
            // Regenerate session to prevent session fixation
            $request->session()->regenerate();
            
            // If remember me is checked, set a longer session lifetime
            if ($request->boolean('remember')) {
                // Set session lifetime to 5 years (525600 minutes)
                config(['session.lifetime' => 525600]);
                
                // Set the remember me cookie
                $request->session()->put('remember_me', true);
            }
            
            return redirect()->intended(route('dashboard.'.auth()->user()->role, absolute: false));
        } catch(\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
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
