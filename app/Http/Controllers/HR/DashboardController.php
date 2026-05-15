<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'assigned_customers' => 0,
            'pending_loans'      => 0,
            'approved_loans'     => 0,
            'rejected_loans'     => 0,
        ];

        return view('hr.dashboard', compact('stats'));
    }
}
