<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAssignment;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hrId = Auth::id();

        $assignedCustomerIds = CustomerAssignment::where('hr_id', $hrId)
            ->where('is_active', true)
            ->pluck('customer_id');

        $stats = [
            'assigned_customers'  => $assignedCustomerIds->count(),
            'pending_loans'       => Loan::whereIn('customer_id', $assignedCustomerIds)->where('status', 'pending')->count(),
            'under_review'        => Loan::whereIn('customer_id', $assignedCustomerIds)->where('status', 'under_review')->count(),
            'approved_loans'      => Loan::whereIn('customer_id', $assignedCustomerIds)->where('status', 'approved')->count(),
            'rejected_loans'      => Loan::whereIn('customer_id', $assignedCustomerIds)->where('status', 'rejected')->count(),
            'disbursed_loans'     => Loan::whereIn('customer_id', $assignedCustomerIds)->where('status', 'disbursed')->count(),
            'pending_documents'   => LoanDocument::whereHas('loan', fn ($q) => $q->whereIn('customer_id', $assignedCustomerIds))
                                        ->where('status', 'pending')->count(),
            'this_month_loans'    => Loan::whereIn('customer_id', $assignedCustomerIds)
                                        ->whereMonth('applied_at', now()->month)
                                        ->whereYear('applied_at', now()->year)
                                        ->count(),
            'this_month_approved' => Loan::whereIn('customer_id', $assignedCustomerIds)
                                        ->where('status', 'approved')
                                        ->whereMonth('applied_at', now()->month)
                                        ->whereYear('applied_at', now()->year)
                                        ->count(),
            'total_pending_amount' => Loan::whereIn('customer_id', $assignedCustomerIds)
                                        ->whereIn('status', ['pending', 'under_review'])
                                        ->sum('amount_requested'),
        ];

        $monthlyStats = collect(range(5, 0))->map(function ($i) use ($assignedCustomerIds) {
            $month = now()->subMonths($i);
            return [
                'label'    => $month->format('M Y'),
                'total'    => Loan::whereIn('customer_id', $assignedCustomerIds)
                                ->whereMonth('applied_at', $month->month)
                                ->whereYear('applied_at', $month->year)
                                ->count(),
                'approved' => Loan::whereIn('customer_id', $assignedCustomerIds)
                                ->where('status', 'approved')
                                ->whereMonth('applied_at', $month->month)
                                ->whereYear('applied_at', $month->year)
                                ->count(),
                'rejected' => Loan::whereIn('customer_id', $assignedCustomerIds)
                                ->where('status', 'rejected')
                                ->whereMonth('applied_at', $month->month)
                                ->whereYear('applied_at', $month->year)
                                ->count(),
            ];
        });

        $recentCustomers = Customer::whereIn('id', $assignedCustomerIds)
            ->withCount('loans')
            ->with(['loans' => fn ($q) => $q->latest('applied_at')->take(1)])
            ->latest()
            ->take(6)
            ->get();

        $recentLoans = Loan::whereIn('customer_id', $assignedCustomerIds)
            ->with('customer')
            ->latest('applied_at')
            ->take(8)
            ->get();

        $recentNotifications = Notification::where('user_id', $hrId)
            ->latest()
            ->take(6)
            ->get();

        $unreadNotifications = Notification::where('user_id', $hrId)
            ->where('is_read', false)
            ->count();

        return view('hr.dashboard', compact(
            'stats',
            'recentCustomers',
            'recentLoans',
            'monthlyStats',
            'recentNotifications',
            'unreadNotifications'
        ));
    }
}
