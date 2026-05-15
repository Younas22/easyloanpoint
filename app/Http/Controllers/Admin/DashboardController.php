<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanStatusHistory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // ── Core stats ────────────────────────────────────────────────────────
        $stats = [
            'total_customers'  => Customer::count(),
            'total_hr'         => User::where('role', 'hr')->count(),
            'total_loans'      => Loan::count(),
            'pending_loans'    => Loan::where('status', 'pending')->count(),
            'under_review'     => Loan::where('status', 'under_review')->count(),
            'approved_loans'   => Loan::where('status', 'approved')->count(),
            'rejected_loans'   => Loan::where('status', 'rejected')->count(),
            'disbursed_loans'  => Loan::where('status', 'disbursed')->count(),
            'total_requested'  => Loan::sum('amount_requested'),
            'total_disbursed'  => Loan::where('status', 'disbursed')->sum('amount_approved'),
            'active_customers' => Customer::where('status', 'active')->count(),
        ];

        // ── Monthly chart data (last 12 months) ───────────────────────────────
        $monthlyChart = $this->buildMonthlyChartData();

        // ── Loan status pie data ──────────────────────────────────────────────
        $statusChart = [
            'labels' => ['Pending', 'Under Review', 'Approved', 'Rejected', 'Disbursed'],
            'data'   => [
                $stats['pending_loans'],
                $stats['under_review'],
                $stats['approved_loans'],
                $stats['rejected_loans'],
                $stats['disbursed_loans'],
            ],
        ];

        // ── Recent applications (searchable + filterable + paginated) ─────────
        $search = $request->input('search', '');
        $status = $request->input('status', '');

        $recentLoans = Loan::with(['customer', 'assignedHR'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('loan_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn($c) =>
                            $c->where('name', 'like', "%{$search}%")
                              ->orWhere('phone', 'like', "%{$search}%")
                        );
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // ── Recent HR activities ──────────────────────────────────────────────
        $recentActivities = LoanStatusHistory::with(['loan.customer', 'changedBy'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'monthlyChart',
            'statusChart',
            'recentLoans',
            'recentActivities',
            'search',
            'status',
        ));
    }

    public function chartData(): JsonResponse
    {
        return response()->json($this->buildMonthlyChartData());
    }

    private function buildMonthlyChartData(): array
    {
        $labels   = [];
        $applied  = [];
        $approved = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[]   = $month->format('M Y');
            $applied[]  = Loan::whereYear('created_at', $month->year)
                              ->whereMonth('created_at', $month->month)
                              ->count();
            $approved[] = Loan::whereYear('created_at', $month->year)
                              ->whereMonth('created_at', $month->month)
                              ->whereIn('status', ['approved', 'disbursed'])
                              ->count();
        }

        return compact('labels', 'applied', 'approved');
    }
}
