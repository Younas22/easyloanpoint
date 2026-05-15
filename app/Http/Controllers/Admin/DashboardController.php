<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users'     => User::whereIn('role', ['admin', 'hr'])->count(),
            'total_hr'        => User::where('role', 'hr')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'active_users'    => User::where('status', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
