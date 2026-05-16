<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    // ── Public actions ────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $filters   = $this->resolveFilters($request);
        $summary   = $this->buildSummary($filters);
        $monthly   = $this->buildMonthly();
        $hrStats   = $this->buildHrStats($filters);
        $loanRows  = $this->buildLoanRows($filters, (int) $request->input('page', 1));
        $loanTypes = Loan::distinct()->orderBy('loan_type')->pluck('loan_type')->filter()->values();
        $hrUsers   = User::where('role', 'hr')->where('status', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.reports.index', compact(
            'filters', 'summary', 'monthly', 'hrStats', 'loanRows', 'loanTypes', 'hrUsers'
        ));
    }

    public function ajax(Request $request): JsonResponse
    {
        $filters = $this->resolveFilters($request);
        $page    = (int) $request->input('page', 1);

        return response()->json([
            'summary'     => $this->buildSummary($filters),
            'hrStats'     => $this->buildHrStats($filters),
            'loanRows'    => $this->buildLoanRows($filters, $page),
            'periodLabel' => $filters['label'],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $filters    = $this->resolveFilters($request);
        $exportType = $request->input('export_type', 'loans');
        $filename   = 'elp_' . $exportType . '_' . now()->format('Ymd_His') . '.csv';

        return response()->stream(
            fn() => $this->streamCsv($filters, $exportType),
            200,
            [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ]
        );
    }

    public function printReport(Request $request): View
    {
        $filters = $this->resolveFilters($request);
        $summary = $this->buildSummary($filters);
        $hrStats = $this->buildHrStats($filters);
        $loans   = $this->allLoansForPrint($filters);

        return view('admin.reports.print', compact('filters', 'summary', 'hrStats', 'loans'));
    }

    // ── Filter resolution ─────────────────────────────────────────────────────

    private function resolveFilters(Request $request): array
    {
        $range = $request->input('range', 'this_month');

        [$from, $to] = match ($range) {
            'today'     => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
            ],
            'this_week' => [
                Carbon::now()->startOfWeek(Carbon::MONDAY),
                Carbon::now()->endOfWeek(Carbon::SUNDAY),
            ],
            'custom'    => [
                Carbon::parse($request->input('date_from', now()->startOfMonth()->format('Y-m-d')))->startOfDay(),
                Carbon::parse($request->input('date_to',   now()->format('Y-m-d')))->endOfDay(),
            ],
            default     => [
                Carbon::now()->startOfMonth()->startOfDay(),
                Carbon::now()->endOfDay(),
            ],
        };

        $label = match ($range) {
            'today'     => 'Today — ' . $from->format('d M Y'),
            'this_week' => 'This Week — ' . $from->format('d M') . ' to ' . $to->format('d M Y'),
            'custom'    => $from->format('d M Y') . ' to ' . $to->format('d M Y'),
            default     => 'This Month — ' . $from->format('F Y'),
        };

        return [
            'range'     => $range,
            'from'      => $from,
            'to'        => $to,
            'date_from' => $from->format('Y-m-d'),
            'date_to'   => $to->format('Y-m-d'),
            'status'    => $request->input('status', ''),
            'loan_type' => $request->input('loan_type', ''),
            'hr_id'     => $request->input('hr_id', ''),
            'search'    => $request->input('search', ''),
            'label'     => $label,
        ];
    }

    // ── Summary data ──────────────────────────────────────────────────────────

    private function buildSummary(array $f): array
    {
        $loanAgg = Loan::whereBetween('applied_at', [$f['from'], $f['to']])
            ->when($f['loan_type'], fn($q) => $q->where('loan_type', $f['loan_type']))
            ->when($f['hr_id'],     fn($q) => $q->where('assigned_hr_id', $f['hr_id']))
            ->when($f['status'],    fn($q) => $q->where('status', $f['status']))
            ->selectRaw("
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'pending'      THEN 1 ELSE 0 END) AS cnt_pending,
                SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) AS cnt_review,
                SUM(CASE WHEN status = 'approved'     THEN 1 ELSE 0 END) AS cnt_approved,
                SUM(CASE WHEN status = 'rejected'     THEN 1 ELSE 0 END) AS cnt_rejected,
                SUM(CASE WHEN status = 'disbursed'    THEN 1 ELSE 0 END) AS cnt_disbursed,
                COALESCE(SUM(amount_requested), 0) AS total_requested,
                COALESCE(SUM(CASE WHEN status IN ('approved','disbursed')
                    THEN COALESCE(amount_approved, 0) ELSE 0 END), 0) AS approved_amount,
                COALESCE(SUM(CASE WHEN status IN ('pending','under_review')
                    THEN COALESCE(amount_requested, 0) ELSE 0 END), 0) AS pending_amount,
                COALESCE(SUM(CASE WHEN status = 'disbursed'
                    THEN COALESCE(amount_approved, 0) ELSE 0 END), 0) AS disbursed_amount
            ")
            ->first();

        $custBase = Customer::query()
            ->when($f['hr_id'], fn($q) => $q->whereHas(
                'assignments',
                fn($a) => $a->where('hr_id', $f['hr_id'])->where('is_active', true)
            ));

        $custAgg = (clone $custBase)
            ->selectRaw("
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'active'   THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) AS inactive
            ")
            ->first();

        $newCustomers = (clone $custBase)
            ->whereBetween('created_at', [$f['from'], $f['to']])
            ->count();

        $hrCount = User::where('role', 'hr')->where('status', true)->count();

        return [
            'loan_total'        => (int) ($loanAgg->total       ?? 0),
            'loan_pending'      => (int) ($loanAgg->cnt_pending  ?? 0),
            'loan_under_review' => (int) ($loanAgg->cnt_review   ?? 0),
            'loan_approved'     => (int) ($loanAgg->cnt_approved ?? 0),
            'loan_rejected'     => (int) ($loanAgg->cnt_rejected ?? 0),
            'loan_disbursed'    => (int) ($loanAgg->cnt_disbursed ?? 0),

            'cust_new'     => $newCustomers,
            'cust_active'  => (int) ($custAgg->active   ?? 0),
            'cust_inactive'=> (int) ($custAgg->inactive ?? 0),
            'cust_total'   => (int) ($custAgg->total    ?? 0),

            'hr_total' => $hrCount,

            'fin_total_req'  => (float) ($loanAgg->total_requested ?? 0),
            'fin_approved'   => (float) ($loanAgg->approved_amount ?? 0),
            'fin_pending'    => (float) ($loanAgg->pending_amount  ?? 0),
            'fin_disbursed'  => (float) ($loanAgg->disbursed_amount ?? 0),

            'pie_labels' => ['Pending', 'Under Review', 'Approved', 'Rejected', 'Disbursed'],
            'pie_data'   => [
                (int) ($loanAgg->cnt_pending  ?? 0),
                (int) ($loanAgg->cnt_review   ?? 0),
                (int) ($loanAgg->cnt_approved ?? 0),
                (int) ($loanAgg->cnt_rejected ?? 0),
                (int) ($loanAgg->cnt_disbursed ?? 0),
            ],
        ];
    }

    // ── Monthly chart (last 12 months, no date-range filter) ──────────────────

    private function buildMonthly(): array
    {
        $labels = $applied = $approved = $disbursed = [];

        for ($i = 11; $i >= 0; $i--) {
            $month  = Carbon::now()->subMonths($i);
            $start  = $month->copy()->startOfMonth();
            $end    = $month->copy()->endOfMonth();

            $labels[] = $month->format('M y');

            $row = Loan::whereBetween('applied_at', [$start, $end])
                ->selectRaw("
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'approved'  THEN 1 ELSE 0 END) AS approved,
                    SUM(CASE WHEN status = 'disbursed' THEN 1 ELSE 0 END) AS disbursed
                ")
                ->first();

            $applied[]   = (int) ($row->total     ?? 0);
            $approved[]  = (int) ($row->approved  ?? 0);
            $disbursed[] = (int) ($row->disbursed ?? 0);
        }

        return compact('labels', 'applied', 'approved', 'disbursed');
    }

    // ── HR performance ────────────────────────────────────────────────────────

    private function buildHrStats(array $f): array
    {
        return User::where('role', 'hr')
            ->where('status', true)
            ->when($f['hr_id'], fn($q) => $q->where('id', $f['hr_id']))
            ->orderBy('name')
            ->withCount([
                'customerAssignments as assigned_customers' => fn($q) => $q->where('is_active', true),
            ])
            ->get(['id', 'name'])
            ->map(function (User $hr) use ($f) {
                $row = Loan::where('assigned_hr_id', $hr->id)
                    ->whereBetween('applied_at', [$f['from'], $f['to']])
                    ->selectRaw("
                        COUNT(*) AS total,
                        SUM(CASE WHEN status = 'approved'  THEN 1 ELSE 0 END) AS approved,
                        SUM(CASE WHEN status = 'rejected'  THEN 1 ELSE 0 END) AS rejected,
                        SUM(CASE WHEN status IN ('pending','under_review') THEN 1 ELSE 0 END) AS pending,
                        SUM(CASE WHEN status = 'disbursed' THEN 1 ELSE 0 END) AS disbursed,
                        COALESCE(SUM(CASE WHEN status IN ('approved','disbursed')
                            THEN COALESCE(amount_approved,0) ELSE 0 END),0) AS approved_amount
                    ")
                    ->first();

                return [
                    'id'                 => $hr->id,
                    'name'               => $hr->name,
                    'initials'           => strtoupper(substr($hr->name, 0, 2)),
                    'assigned_customers' => (int) $hr->assigned_customers,
                    'total_loans'        => (int) ($row->total    ?? 0),
                    'approved'           => (int) ($row->approved  ?? 0),
                    'rejected'           => (int) ($row->rejected  ?? 0),
                    'pending'            => (int) ($row->pending   ?? 0),
                    'disbursed'          => (int) ($row->disbursed ?? 0),
                    'approved_amount'    => (float) ($row->approved_amount ?? 0),
                ];
            })
            ->toArray();
    }

    // ── Loan rows (paginated) ─────────────────────────────────────────────────

    private function buildLoanRows(array $f, int $page = 1): array
    {
        $paginated = Loan::with(['customer:id,name,phone', 'assignedHR:id,name'])
            ->whereBetween('applied_at', [$f['from'], $f['to']])
            ->when($f['status'],    fn($q) => $q->where('status', $f['status']))
            ->when($f['loan_type'], fn($q) => $q->where('loan_type', $f['loan_type']))
            ->when($f['hr_id'],     fn($q) => $q->where('assigned_hr_id', $f['hr_id']))
            ->when($f['search'],    function ($q) use ($f) {
                $s = $f['search'];
                $q->where(function ($inner) use ($s) {
                    $inner->where('loan_number', 'like', "%{$s}%")
                          ->orWhereHas('customer', fn($c) =>
                              $c->where('name', 'like', "%{$s}%")
                                ->orWhere('phone', 'like', "%{$s}%")
                          );
                });
            })
            ->latest('applied_at')
            ->paginate(15, ['id', 'loan_number', 'customer_id', 'assigned_hr_id', 'loan_type', 'amount_requested', 'amount_approved', 'status', 'applied_at'], 'page', $page);

        return [
            'data' => $paginated->getCollection()->map(fn(Loan $l) => [
                'loan_number'     => $l->loan_number,
                'customer_name'   => $l->customer?->name  ?? '—',
                'customer_phone'  => $l->customer?->phone ?? '—',
                'loan_type_label' => $l->loan_type_label,
                'amount'          => (float) $l->amount_requested,
                'amount_approved' => (float) ($l->amount_approved ?? 0),
                'status'          => $l->status,
                'status_label'    => $l->status_label,
                'hr_name'         => $l->assignedHR?->name ?? '—',
                'applied_at'      => $l->applied_at?->format('d M Y') ?? '—',
            ])->values()->toArray(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'total'        => $paginated->total(),
            'from'         => $paginated->firstItem() ?? 0,
            'to'           => $paginated->lastItem()  ?? 0,
            'per_page'     => $paginated->perPage(),
        ];
    }

    // ── Print query ───────────────────────────────────────────────────────────

    private function allLoansForPrint(array $f)
    {
        return Loan::with(['customer:id,name,phone', 'assignedHR:id,name'])
            ->whereBetween('applied_at', [$f['from'], $f['to']])
            ->when($f['status'],    fn($q) => $q->where('status', $f['status']))
            ->when($f['loan_type'], fn($q) => $q->where('loan_type', $f['loan_type']))
            ->when($f['hr_id'],     fn($q) => $q->where('assigned_hr_id', $f['hr_id']))
            ->latest('applied_at')
            ->limit(500)
            ->get(['id', 'loan_number', 'customer_id', 'assigned_hr_id', 'loan_type', 'amount_requested', 'amount_approved', 'status', 'applied_at']);
    }

    // ── CSV export ────────────────────────────────────────────────────────────

    private function streamCsv(array $f, string $type): void
    {
        $out = fopen('php://output', 'w');
        fprintf($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

        match ($type) {
            'customers' => $this->csvCustomers($out, $f),
            'hr'        => $this->csvHr($out, $f),
            'financial' => $this->csvFinancial($out, $f),
            default     => $this->csvLoans($out, $f),
        };

        fclose($out);
    }

    private function csvLoans($out, array $f): void
    {
        fputcsv($out, ['Loan #', 'Customer Name', 'Phone', 'Loan Type', 'Amount Requested (₹)', 'Amount Approved (₹)', 'Status', 'HR Manager', 'Applied Date']);

        Loan::with(['customer:id,name,phone', 'assignedHR:id,name'])
            ->whereBetween('applied_at', [$f['from'], $f['to']])
            ->when($f['status'],    fn($q) => $q->where('status', $f['status']))
            ->when($f['loan_type'], fn($q) => $q->where('loan_type', $f['loan_type']))
            ->when($f['hr_id'],     fn($q) => $q->where('assigned_hr_id', $f['hr_id']))
            ->latest('applied_at')
            ->chunk(200, function ($loans) use ($out) {
                foreach ($loans as $l) {
                    fputcsv($out, [
                        $l->loan_number,
                        $l->customer?->name ?? '',
                        $l->customer?->phone ?? '',
                        $l->loan_type_label,
                        number_format((float) $l->amount_requested, 2),
                        number_format((float) ($l->amount_approved ?? 0), 2),
                        $l->status_label,
                        $l->assignedHR?->name ?? '',
                        $l->applied_at?->format('d/m/Y') ?? '',
                    ]);
                }
            });
    }

    private function csvCustomers($out, array $f): void
    {
        fputcsv($out, ['Name', 'Email', 'Phone', 'City', 'State', 'Employment Type', 'Status', 'Joined Date']);

        Customer::whereBetween('created_at', [$f['from'], $f['to']])
            ->when($f['hr_id'], fn($q) => $q->whereHas(
                'assignments',
                fn($a) => $a->where('hr_id', $f['hr_id'])->where('is_active', true)
            ))
            ->chunk(200, function ($customers) use ($out) {
                foreach ($customers as $c) {
                    fputcsv($out, [
                        $c->name, $c->email, $c->phone,
                        $c->city ?? '', $c->state ?? '',
                        $c->employment_type ?? '', $c->status,
                        $c->created_at->format('d/m/Y'),
                    ]);
                }
            });
    }

    private function csvHr($out, array $f): void
    {
        fputcsv($out, ['HR Manager', 'Assigned Customers', 'Total Loans', 'Approved', 'Rejected', 'Pending/Review', 'Disbursed', 'Approved Amount (₹)']);

        foreach ($this->buildHrStats($f) as $hr) {
            fputcsv($out, [
                $hr['name'],
                $hr['assigned_customers'],
                $hr['total_loans'],
                $hr['approved'],
                $hr['rejected'],
                $hr['pending'],
                $hr['disbursed'],
                number_format($hr['approved_amount'], 2),
            ]);
        }
    }

    private function csvFinancial($out, array $f): void
    {
        $s = $this->buildSummary($f);
        fputcsv($out, ['EasyLoanPoint — Financial Report']);
        fputcsv($out, ['Period: ' . $f['label']]);
        fputcsv($out, ['Generated: ' . now()->format('d M Y H:i')]);
        fputcsv($out, []);
        fputcsv($out, ['Metric', 'Amount (₹)']);
        fputcsv($out, ['Total Loan Amount Requested', number_format($s['fin_total_req'],  2)]);
        fputcsv($out, ['Approved Amount',             number_format($s['fin_approved'],   2)]);
        fputcsv($out, ['Pending Amount',              number_format($s['fin_pending'],    2)]);
        fputcsv($out, ['Disbursed Amount',            number_format($s['fin_disbursed'],  2)]);
        fputcsv($out, []);
        fputcsv($out, ['Loan Status Breakdown']);
        fputcsv($out, ['Status', 'Count']);
        fputcsv($out, ['Pending',      $s['loan_pending']]);
        fputcsv($out, ['Under Review', $s['loan_under_review']]);
        fputcsv($out, ['Approved',     $s['loan_approved']]);
        fputcsv($out, ['Rejected',     $s['loan_rejected']]);
        fputcsv($out, ['Disbursed',    $s['loan_disbursed']]);
        fputcsv($out, ['Total',        $s['loan_total']]);
        fputcsv($out, []);
        $m = $this->buildMonthly();
        fputcsv($out, ['Monthly Breakdown (Last 12 Months)']);
        fputcsv($out, ['Month', 'Applied', 'Approved', 'Disbursed']);
        foreach ($m['labels'] as $i => $label) {
            fputcsv($out, [$label, $m['applied'][$i], $m['approved'][$i], $m['disbursed'][$i]]);
        }
    }
}
