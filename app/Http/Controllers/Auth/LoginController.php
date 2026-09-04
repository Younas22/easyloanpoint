<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Invalid email or password. Please try again.');
        }

        $user = Auth::user();

        if (!$user->isActive()) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Your account has been deactivated. Contact administrator.');
        }

        if ($user->isCustomer()) {
            Auth::logout();
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Please use the mobile app to access your account.');
        }

        $request->session()->regenerate();

        ActivityLogService::loginSuccess($user->name);

        return $this->redirectByRole();
    }

    public function logout(Request $request): RedirectResponse
    {
        $name = Auth::user()?->name ?? 'User';
        ActivityLogService::logout($name);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    private function redirectByRole(): RedirectResponse
    {
        return match(Auth::user()->role) {
            'admin', 'super_admin' => redirect()->route('admin.dashboard'),
            'hr'                   => redirect()->route('hr.dashboard'),
            default                => redirect()->route('login'),
        };
    }
}
